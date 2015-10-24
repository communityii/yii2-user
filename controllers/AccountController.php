<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\authclient\AuthAction;
use comyii\user\Module;
use comyii\user\models\LoginForm;
use comyii\user\models\RecoveryForm;
use comyii\user\models\User;
use comyii\user\models\SocialProfile;
use comyii\user\events\RegistrationEvent;
use comyii\user\events\LoginEvent;


/**
 * Account controller for authentication of various user actions.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class AccountController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'register', 'recovery', 'password'],
                'rules' => [
                    [
                        'actions' => ['register', 'recovery'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['password', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = [];
        if ($this->getConfig('socialSettings', 'enabled', false)) {
            $actions['auth'] = [
                'class' => AuthAction::classname(),
                'successCallback' => [$this, 'onAuthSuccess'],
            ];
        }
        $captcha = $this->getConfig('registrationSettings', 'captcha', false);
        if ($captcha !== false) {
            $captcha = ArrayHelper::getValue($captcha, 'action', []);
            $actions['captcha'] = $captcha;
        }
        return $actions;
    }

    /**
     * Social client authorization callback
     *
     * @param \yii\authclient\BaseClient $client
     */
    public function onAuthSuccess($client)
    {
        /**
         * @var SocialProfile $socialClass
         * @var User          $userClass
         * @var SocialProfile $auth
         * @var User          $user
         */
        $attributes = $client->getUserAttributes();
        $clientId = $client->getId();
        $clientTitle = $client->getTitle();
        $session = Yii::$app->session;
        $userClass = $this->fetchModel(Module::MODEL_USER);
        $socialClass = $this->fetchModel(Module::MODEL_SOCIAL_PROFILE);
        $auth = $socialClass::find()->where([
            'source' => $clientId,
            'source_id' => $attributes['id'],
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                $user = $auth->user;
                Yii::$app->user->login($user);
                $session->setFlash('success', Yii::t(
                    'user',
                    'Logged in successfully with your <b>{client}</b> account.',
                    ['client' => $clientTitle]
                ));
            } else { // signup
                if (isset($attributes['email']) && isset($attributes['username']) &&
                    $userClass::find()->where(['email' => $attributes['email']])->exists()
                ) {
                    $session->setFlash('error', Yii::t(
                        'user',
                        'User with the same email as in <b>{client}</b> account already exists but is not linked to it. Login using email first to link it.',
                        ['client' => $clientTitle]
                    ));
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
                    $user = new $userClass([
                        'username' => $attributes['login'],
                        'email' => $attributes['email'],
                        'password' => $password,
                    ]);
                    $user->generateAuthKey();
                    $success = false;
                    $transaction = $user->getDb()->beginTransaction();
                    if ($user->save()) {
                        $auth = new $socialClass([
                            'user_id' => $user->id,
                            'source' => $clientId,
                            'source_id' => (string)$attributes['id'],
                        ]);
                        if ($auth->save()) {
                            $transaction->commit();
                            $success = true;
                            Yii::$app->user->login($user);
                        }
                    }
                    if (!$success) {
                        $transaction->rollBack();
                        $session->setFlash('error', Yii::t(
                            'user',
                            'Error while authenticating <b>{client}</b> account.<pre>{errors}</pre>',
                            ['client' => $clientTitle, 'errors' => print_r($user->getErrors(), true)]
                        ));
                    } else {
                        $session->setFlash('success', Yii::t(
                            'user',
                            'Logged in successfully with your <b>{client}</b> account.',
                            ['client' => $clientTitle]
                        ));
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $user = Yii::$app->user;
                $id = $user->id;
                $auth = new $socialClass([
                    'user_id' => $id,
                    'source' => $clientId,
                    'source_id' => $attributes['id'],
                ]);
                if ($auth->save()) {
                    $session->setFlash('success', Yii::t(
                        'user',
                        'Successfully authenticated <b>{client}</b> account for <b>{user}</b>.',
                        ['client' => $clientTitle, 'user' => $user->username]
                    ));
                } else {
                    $session->setFlash('error', Yii::t(
                        'user',
                        'Error while authenticating <b>{client}</b> account for <b>{user}</b>.<pre>{errors}</pre>',
                        ['client' => $clientTitle, 'errors' => print_r($auth->getErrors(), true)]
                    ));
                }
            } else {
                 $session->setFlash('success', Yii::t(
                    'user',
                    'You have already connected your <b>{client}</b> account previously. Logged in successfully.',
                    ['client' => $clientTitle]
                ));
            }
        }
    }

    /**
     * Login the current user after validating credentials, the password expiry, and password lock status.
     *
     * @return string|\yii\web\Response
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        /**
         * @var LoginForm $model
         * @var Module    $m
         */
        $app = Yii::$app;
        $m = $this->_module;
        $event = new LoginEvent;
        if (!$app->user->isGuest) {
            $event->result = LoginEvent::RESULT_ALREADY_AUTH;
            $m->trigger(Module::EVENT_LOGIN_COMPLETE, $event);
            if ($app->user->returnUrl == Url::to([$this->fetchAction(Module::ACTION_LOGOUT)])) {
                return $event->redirect ? $this->redirect($event->redirect) : $this->goHome();
            }
            return $event->redirect ? $this->redirect($event->redirect) : $this->goBack();
        }
        $hasSocialAuth = $m->hasSocialAuth();
        $authAction = $this->fetchAction(Module::ACTION_SOCIAL_AUTH);
        $class = $this->fetchModel(Module::MODEL_LOGIN);
        $post = $app->request->post();
        $model = new $class();
        $event->unlockExpiry = !empty($post) && !empty($post['unlock-account']);
        $model->scenario = $event->unlockExpiry ? Module::SCN_EXPIRY : Module::SCN_LOGIN;
        $event->model = $model;
        $event->redirect = $this->getConfig('loginSettings', 'loginRedirectUrl');
        $event->authAction = $authAction;
        $event->hasSocial = $hasSocialAuth;
        $m->trigger(Module::EVENT_LOGIN_BEGIN, $event);
        $viewFile = $event->viewFile? $event->viewFile : Module::VIEW_REGISTER;
        try {
            if ($event->transaction) {
                $transaction = Yii::$app->db->beginTransaction();
            }
            if ($model->load($post) && $model->validate() && !$event->error) {
                $event->handled = false;
                $session = $app->session;
                $user = $model->getUser();
                $event->user = $user;
                if ($event->unlockExpiry) {
                    $user->setPassword($model->password_new);
                    $user->status_sec = null;
                    $user->save(false);
                    $event->flashType = 'success';
                    $event->message = Yii::t('user', 'Your password has been changed successfully and you have been logged in.');
                    $model->login($user);
                    $user->setLastLogin();
                    if($event->transaction) {
                        $transaction->commit();
                    }
                    $event->newPassword = true;
                    $event->result = LoginEvent::RESULT_SUCCESS;
                    $m->trigger(Module::EVENT_LOGIN_COMPLETE, $event);
                    $session->setFlash(
                        $event->flashType,
                        $event->message
                    );
                    if ($event->redirect) {
                        return $this->redirect([$event->redirect]);
                    } else {
                        return $this->safeRedirect();
                    }
                }
                $event->status = $model->login($user);
                if ($event->status === Module::STATUS_EXPIRED) {
                    $event->result = LoginEvent::RESULT_EXPIRED;
                    $event->flashType = 'error';
                    $event->message = Yii::t('user', 'Your password has expired. Change your password by completing the details below.');
                    $model->scenario = Module::SCN_EXPIRY;
                } elseif ($event->status === Module::STATUS_LOCKED) {
                    $event->result = LoginEvent::RESULT_LOCKED;
                    $event->flashType = 'error';
                    $link = Yii::t('user', 'Click {link} to reset your password and unlock your account.', [
                        'link' => Html::a(Yii::t('user', 'here'), $this->fetchAction(Module::ACTION_RECOVERY))
                    ]);
                    $event->message = Yii::t(
                        'user',
                        'Your account has been locked due to multiple invalid login attempts. {reset}',
                        ['reset' => $link]
                    );
                } elseif ($event->status) {
                    $user->setLastLogin();
                    if ($event->transaction) {
                        $transaction->commit();
                    }
                    $event->result = LoginEvent::RESULT_SUCCESS;
                    $m->trigger(Module::EVENT_LOGIN_COMPLETE, $event);
                    if ($event->redirect) {
                        return $this->redirect([$event->redirect]);
                    } else {
                        return $this->safeRedirect();
                    }
                }
            }
            if($event->message) {
                $session->setFlash(
                    $event->flashType,
                    $event->message
                );
            }
            $m->trigger(Module::EVENT_LOGIN_COMPLETE, $event);
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return $this->display($viewFile, [
            'model' => $model,
            'hasSocialAuth' => $hasSocialAuth,
            'authAction' => $authAction,
            'loginTitle' => $model->scenario === Module::SCN_EXPIRY ? Yii::t('user', 'Change Password') :
                Yii::t('user', 'Login'),
            'authTitle' => Yii::t('user', 'Or Login Using')
        ]);
    }

    /**
     * User logout action
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        $url = $this->getConfig('loginSettings', 'logoutRedirectUrl');
        Yii::$app->user->logout();
        return ($url == null) ? $this->goHome() : $this->redirect($url);
    }

    /**
     * User registration action
     *
     * @return string|\yii\web\Response
     * @throws InvalidConfigException
     */
    public function actionRegister($type='user')
    {
        /**
         * @var User   $model
         * @var Module $m
         */
        $m = $this->_module;
        $config = $m->registrationSettings;
        if (!$config['enabled']) {
            return $this->goBack();
        }
        $session = Yii::$app->session;
        $hasSocialAuth = $m->hasSocialAuth();
        $authAction = $this->fetchAction(Module::ACTION_SOCIAL_AUTH);
        $class = $this->fetchModel(Module::MODEL_USER);
        $model = new $class(['scenario' => Module::SCN_REGISTER]);
        $event = new RegistrationEvent;
        $event->type = $type;
        $event->model = $model;
        $m->trigger(Module::EVENT_REGISTER_BEGIN, $event);
        $viewFile = $event->viewFile? $event->viewFile : Module::VIEW_REGISTER;
        if ($model->load(Yii::$app->request->post()) && !$event->error) {
            $model->setPassword($model->password);
            $model->generateAuthKey();
            $model->status = Module::STATUS_PENDING;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $valid = false;
                if($model->save() && !$event->error) {
                    $transaction->commit();
                    $valid=true;
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
            if ($valid) {
                $event->flashType = 'success';
                $activate = $event->activate !== null ? $event->activate : $config['autoActivate'];
                if ($activate && Yii::$app->user->login($model)) {
                    $model->status = Module::STATUS_ACTIVE;
                    $model->setLastLogin();
                    $event->isActivated = true;
                    $event->message = Yii::t('user','The user <b>{user}</b> was registered successfully. You have been logged in.',
                        ['user' => $model->username]);
                } else {
                    $timeLeft = Module::timeLeft('activation', $model->activationKeyExpiry);
                    if ($model->sendEmail('activation', $timeLeft)) {
                        $event->message = Yii::t('user','Instructions for activating your account has been sent to your email <b>{email}</b>. {timeLeft}',
                            ['email' => $model->email, 'timeLeft' => $timeLeft]);
                    } else {
                        $event->flashType = 'warning';
                        $event->message = Yii::t('user','Could not send activation instructions to your email <b>{email}</b>. Retry again later.',
                            ['email' => $model->email]);
                    }
                }
                $event->handled = false; // reuse event object
                $m->trigger(Module::EVENT_REGISTER_COMPLETE,$event);
                $session->setFlash($event->flashType ? $event->flashType : '', $event->message);
                return $event->redirect ? $this->redirect($event->redirect) : $this->goHome();
            }
        }
        return $this->display($viewFile, [
            'model' => $model,
            'hasSocialAuth' => $hasSocialAuth,
            'authAction' => $authAction,
            'registerTitle' => Yii::t('user', 'Register'),
            'authTitle' => Yii::t('user', 'Or Login Using')
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return string|\yii\web\Response
     */
    public function actionRecovery()
    {
        /**
         * @var RecoveryForm $model
         * @var User         $class
         * @var User         $user
         */
        $class = $this->fetchModel(Module::MODEL_RECOVERY);
        $model = new $class();
        $session = Yii::$app->session;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $class = $this->fetchModel(Module::MODEL_USER);
            $user = $class::findByEmail($model->email);
            $proceed = true;
            if (!$class::isKeyValid($user->reset_key, $user->resetKeyExpiry)) {
                $user->scenario = Module::SCN_RECOVERY;
                $user->generateResetKey();
                $proceed = $user->save();
            }
            $timeLeft = Module::timeLeft('reset', $user->resetKeyExpiry);
            if ($proceed && $user->sendEmail('recovery', $timeLeft)) {
                $session->setFlash('success', Yii::t(
                    'user',
                    'Check your email for further instructions to reset your password. {timeLeft}',
                    ['timeLeft' => $timeLeft]
                ));
                return $this->goHome();
            } else {
                $session->setFlash('error', Yii::t(
                    'user',
                    'Sorry, the password cannot be reset for the email provided. Retry again later.'
                ));
            }
        }
        return $this->display(Module::VIEW_RECOVERY, [
            'model' => $model,
        ]);
    }

    /**
     * Change password for currently logged in user
     *
     * @return string|\yii\web\Response
     */
    public function actionPassword()
    {
        /**
         * @var User $model
         */
        $model = Yii::$app->user->identity;
        $model->scenario = Module::SCN_CHANGEPASS;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->password_new);
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('user', 'The password was changed successfully.'));
            $action = $this->fetchAction(Module::ACTION_PROFILE_INDEX);
            return $this->redirect([$action]);
        }
        return $this->display(Module::VIEW_PASSWORD, [
            'model' => $model,
        ]);
    }

    /**
     * Activates user account
     *
     * @param string $key the activation auth key
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionActivate($key)
    {
        $model = $this->getUserByKey('auth', $key);
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('user', 'The activation link is invalid or expired'));
        }
        $model->scenario = Module::SCN_ACTIVATE;
        $model->status = Module::STATUS_ACTIVE;
        $model->password_reset_on = call_user_func($this->_module->now);
        $model->reset_key = null;
        $session = Yii::$app->session;
        if ($model->save()) {
            $session->setFlash(
                'success',
                Yii::t('user', 'The account was activated successfully. You can proceed to login.')
            );
            $action = $this->fetchAction(Module::ACTION_LOGIN);
            return $this->redirect([$action]);
        } else {
            $session->setFlash('error', Yii::t('user', 'Could not activate the account. Please try again later or contact us.'));
        }
        return $this->goHome();
    }

    /**
     * Reset user account password
     *
     * @param string $key the reset key
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionReset($key)
    {
        $model = $this->getUserByKey('reset', $key);
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('user', 'The password reset link is invalid or expired'));
        }
        $model->scenario = Module::SCN_RESET;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->password_new);
            $model->unlock();
            $model->reset_key = null;
            $session = Yii::$app->session;
            if ($model->save()) {
                $session->setFlash('success', Yii::t(
                    'user',
                    'The password was reset successfully. You can proceed to login with your new password.'
                ));
                return $this->redirect([$this->fetchAction(Module::ACTION_LOGIN)]);
            } else {
                $session->setFlash('error', Yii::t('user', 'Could not reset the password. Please try again later or contact us.'));
            }
        }
        return $this->display(Module::VIEW_RESET, [
            'model' => $model,
        ]);
    }


    /**
     * Confirm new email change for user
     *
     * @param string $key the email change key
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionNewemail($key)
    {
        $model = $this->getUserByKey('email_change', $key);
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('user', 'The email change confirmation link is invalid or expired'));
        }
        $model->scenario = Module::SCN_NEWEMAIL;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->email = $model->email_new;
            $model->email_new = null;
            $model->email_change_key = null;
            $session = Yii::$app->session;
            if ($model->save()) {
                $session->setFlash('success', Yii::t('user', 'The email address was changed successfully.'));
                $action = $this->fetchAction(Module::ACTION_PROFILE_INDEX);
                return $this->redirect([$action]);
            } else {
                $session->setFlash(
                    'error',
                    Yii::t('user', 'Could not confirm the new email address. Please try again later or contact us.')
                );
            }
        }
        return $this->display(Module::VIEW_NEWEMAIL, [
            'model' => $model,
        ]);
    }

    /**
     * Gets user model by key type
     *
     * @param string $type the type of key to fetch
     * @param string $key the key value
     *
     * @return User the user model if found (or null)
     */
    protected function getUserByKey($type, $key)
    {
        /**
         * @var User $class
         */
        if ($type !== 'auth' && $type !== 'reset' && $type !== 'email_change') {
            return null;
        }
        $class = $this->fetchModel(Module::MODEL_USER);
        $attribute = "{$type}_key";
        return $class::findByKey($attribute, $key);
    }
}
