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
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\authclient\AuthAction;
use comyii\user\Module;
use comyii\user\models\LoginForm;
use comyii\user\models\RecoveryForm;
use comyii\user\models\User;
use comyii\user\models\SocialProfile;

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
                        $transaction->rollback();
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
        $m = $this->module;
        if (!$app->user->isGuest) {
            return $this->goBack();
        }
        $hasSocialAuth = $m->hasSocialAuth();
        $authAction = $this->fetchAction(Module::ACTION_SOCIAL_AUTH);
        $class = $this->fetchModel(Module::MODEL_LOGIN);
        $post = $app->request->post();
        $model = new $class();
        $unlockExpiry = !empty($post) && !empty($post['unlock-account']);
        $model->scenario = $unlockExpiry ? Module::SCN_EXPIRY : Module::SCN_LOGIN;
        if ($model->load($post) && $model->validate()) {
            $session = $app->session;
            $user = $model->getUser();
            if ($unlockExpiry) {
                $user->setPassword($model->password_new);
                $user->status_sec = null;
                $user->save(false);
                $session->setFlash(
                    'success',
                    Yii::t('user', 'Your password has been changed successfully and you have been logged in.')
                );
                $model->login($user);
                $user->setLastLogin();
                return $this->safeRedirect();
            }
            $status = $model->login($user);
            if ($status === Module::STATUS_EXPIRED) {
                $session->setFlash(
                    'error',
                    Yii::t('user', 'Your password has expired. Change your password by completing the details below.')
                );
                $model->scenario = Module::SCN_EXPIRY;
            } elseif ($status === Module::STATUS_LOCKED) {
                $link = Yii::t('user', 'Click {link} to reset your password and unlock your account.', [
                    'link' => Html::a(Yii::t('user', 'here'), $this->fetchAction(Module::ACTION_RECOVERY))
                ]);
                $session->setFlash('error', Yii::t(
                    'user',
                    'Your account has been locked due to multiple invalid login attempts. {reset}',
                    ['reset' => $link]
                ));
            } elseif ($status) {
                $user->setLastLogin();
                return $this->safeRedirect();
            }
        }
        return $this->display(Module::VIEW_LOGIN, [
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
    public function actionRegister()
    {
        /**
         * @var User   $model
         * @var Module $m
         */
        $m = $this->module;
        $config = $m->registrationSettings;
        if (!$config['enabled']) {
            return $this->goBack();
        }
        $session = Yii::$app->session;
        $hasSocialAuth = $m->hasSocialAuth();
        $authAction = $this->fetchAction(Module::ACTION_SOCIAL_AUTH);
        $class = $this->fetchModel(Module::MODEL_USER);
        $model = new $class(['scenario' => Module::SCN_REGISTER]);
        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password);
            $model->generateAuthKey();
            $model->status = Module::STATUS_PENDING;
            if ($model->save()) {
                if ($config['autoActivate'] && Yii::$app->user->login($model)) {
                    $model->status = Module::STATUS_ACTIVE;
                    $model->setLastLogin();
                    $session->setFlash('success', Yii::t(
                        'user',
                        'The user <b>{user}</b> was registered successfully. You have been logged in.',
                        ['user' => $model->username]
                    ));
                } else {
                    $timeLeft = Module::timeLeft('activation', $model->activationKeyExpiry);
                    if ($model->sendEmail('activation', $timeLeft)) {
                        $session->setFlash('success', Yii::t(
                            'user',
                            'Instructions for activating your account has been sent to your email <b>{email}</b>. {timeLeft}',
                            ['email' => $model->email, 'timeLeft' => $timeLeft]
                        ));
                    } else {
                        $session->setFlash('warning', Yii::t(
                            'user',
                            'Could not send activation instructions to your email <b>{email}</b>. Retry again later.',
                            ['email' => $model->email]
                        ));
                    }
                }
                return $this->goHome();
            }
        }
        return $this->display(Module::VIEW_REGISTER, [
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
        $model->password_reset_on = call_user_func($this->module->now);
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
            $session->setFlash('error', Yii::t('user', 'Could not activate the account. Please try again later.'));
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
                $session->setFlash('error', Yii::t('user', 'Could not reset the password. Please try again later.'));
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
                    Yii::t('user', 'Could not confirm the new email address. Please try again later.')
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
        if ($type !== 'activation' && $type !== 'reset' && $type !== 'email_change') {
            return null;
        }
        $class = $this->fetchModel(Module::MODEL_USER);
        $attribute = "{$type}_key";
        return $class::findByKey($attribute, $key);
    }
}