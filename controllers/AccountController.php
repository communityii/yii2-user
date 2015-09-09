<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
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
use comyii\user\models\User;

/**
 * Account controller for authentication of various user actions.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class AccountController extends BaseController
{
    /**
     * Account controller behaviors
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'register', 'recovery', 'captcha'],
                'rules' => [
                    [
                        'actions' => ['register', 'recovery', 'captcha'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
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
     * Captcha and other actions
     *
     * @return mixed
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
            $actions['captcha'] =  $captcha;
        }
        return $actions;
    }

    /**
     * Social client authorization callback
     * @param yii\authclient\Client $client
     */
    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();
        $clientId = $client->getId();
        $clientTitle = $client->getTitle();
        $session = Yii::$app->session;
        $userClass = $this->getConfig('modelSettings', Module::MODEL_USER);
        $socialClass = $this->getConfig('modelSettings', Module::MODEL_SOCIAL_PROFILE);

        /** @var Auth $auth */
        $auth = $socialClass::find()->where([
            'source' => $clientId,
            'source_id' => $attributes['id'],
        ])->one();
        
        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                $user = $auth->user;
                Yii::$app->user->login($user);
            } else { // signup
                if (isset($attributes['email']) && isset($attributes['username']) && 
                    $userClass::find()->where(['email' => $attributes['email']])->exists()) {
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
                            'Successfully authenticated <b>{client}</b> account.', 
                            ['client' => $clientTitle]
                        ));
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $id = Yii::$app->user->id;
                $user = $userClass::findOne($id);
                $auth = new $socialClass([
                    'user_id' => $id,
                    'source' => $clientId,
                    'source_id' => $attributes['id'],
                ]);
                if ($auth->save()) {
                    $session->setFlash('success', Yii::t(
                        'user',
                        'Successfully authenticated <b>{client}</b> account for <b>{user}</b>.', 
                        ['client' => $clientTitle]
                    ));
                } else {
                    $session->setFlash('error', Yii::t(
                        'user',
                        'Error while authenticating <b>{client}</b> account for <b>{user}</b>.<pre>{errors}</pre>', 
                        ['client' => $clientTitle, 'errors' => print_r($auth->getErrors(), true)]
                    ));
                }
            }
        }
    }

    /**
     * User login action
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goBack();
        }
        $url = $this->getConfig('loginSettings', 'loginRedirectUrl');
        $hasSocialAuth = $this->getConfig('socialSettings', 'enabled', false);
        $authAction = $this->getConfig('actionSettings', Module::ACTION_SOCIAL_AUTH);
        if ($hasSocialAuth && empty(Yii::$app->authClientCollection) && empty(Yii::$app->authClientCollection->clients)) {
            throw new InvalidConfigException("You must setup the `authClientCollection` component and its `clients` in your app configuration file.");
        }
        $this->layout = $this->getConfig('layoutSettings', Module::ACTION_LOGIN);
        $class = $this->getConfig('modelSettings', Module::MODEL_LOGIN);
        $model = new $class;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $model->getUser();
            $link = Yii::t('user', 'Click {link} to reset your password.', [
                'link' => Html::a(Yii::t('user', 'here'), Module::ACTION_RESET)
            ]);
            $expiredMsg = Yii::t('user', 'Your password has expired. {reset}', ['reset' => $link]);
            $lockedMsg = Yii::t('user', 'Your account has been locked. {reset}', ['reset' => $link]);
            if ($user->status === User::STATUS_INACTIVE) {
                $msg = ($user->isPasswordExpired()) ? $expiredMsg : $lockedMsg;
                return $this->lockAccount(null, $msg);
            } elseif ($user->isPasswordExpired()) {
                return $this->lockAccount($user, $expiredMsg);
            } elseif ($user->isAccountLocked()) {
                return $this->lockAccount($user, $lockedMsg);
            } elseif ($model->login($user)) {
                $user->setLastLogin();
                return $this->safeRedirect();
            }
        }
        return $this->render(Module::SCN_LOGIN, [
            'model' => $model, 
            'hasSocialAuth' => $hasSocialAuth,
            'authAction' => $authAction,
            'loginTitle' => Yii::t('user', 'Login'),
            'authTitle' => Yii::t('user', 'Or Login Using')
        ]);
    }

    /**
     * User logout action
     *
     * @return mixed
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
     * @return mixed
     */
    public function actionRegister()
    {
        $this->layout = $this->getConfig('layoutSettings', Module::ACTION_REGISTER);
        $config = $this->module->registrationSettings;
        if (!$config['enabled']) {
            return $this->goBack();
        }
        $session = Yii::$app->session;
        $hasSocialAuth = $this->getConfig('socialSettings', 'enabled', false);
        $authAction = $this->getConfig('actionSettings', Module::ACTION_SOCIAL_AUTH);
        if ($hasSocialAuth && empty(Yii::$app->authClientCollection) && empty(Yii::$app->authClientCollection->clients)) {
            throw new InvalidConfigException("You must setup the `authClientCollection` component and its `clients` in your app configuration file.");
        }
        $this->layout = $this->getConfig('layoutSettings', Module::ACTION_REGISTER);
        $class = $this->getConfig('modelSettings', Module::MODEL_USER);
        $model = new $class(['scenario' => Module::SCN_REGISTER]);
        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password);
            $model->generateAuthKey();
            $model->status = User::STATUS_PENDING;
            if ($model->save()) {
                if ($config['autoActivate'] && Yii::$app->user->login($model)) {
                    $model->status = User::STATUS_ACTIVE;
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
                            'Could not send activation instructions to your email <b>{email}</b>. Contact the system administrator or retry with a valid email for processing the registration.', 
                            ['email' => $model->email]
                        ));
                    }
                }
                return $this->goHome();
            }
        }
        return $this->render(Module::SCN_REGISTER, [
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
     * @return mixed
     */
    public function actionRecovery()
    {
        $class = $this->getConfig('modelSettings', Module::MODEL_RECOVERY);
        $model = new $class();
        $this->layout = $this->getConfig('layoutSettings', Module::ACTION_RECOVERY);
        $session = Yii::$app->session;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $class = $this->getConfig('modelSettings', Module::MODEL_USER);
            /* @var $user User */
            $user = $class::findOne([
                'status' => User::STATUS_ACTIVE,
                'email' => $model->email,
            ]);
            $proceed = true;
            if (!$class::isKeyValid($user->reset_key, $user->resetKeyExpiry)) {
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
        return $this->render('recovery', [
            'model' => $model,
        ]);
    }

    /**
     * Change password.
     *
     * @return mixed
     */
    public function actionPassword()
    {
        $model = Yii::$app->user->identity;
        $model->scenario = Module::SCN_CHANGEPASS;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->password_new);
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('user', 'The password was changed successfully.'));
            $action = $this->getConfig('actionSettings', Module::ACTION_PROFILE_INDEX);
            return $this->redirect([$action]);
        }
        return $this->render('password', [
            'model' => $model,
        ]);
    }

    /**
     * Reset password.
     *
     * @return mixed
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
            $model->reset_key = null;
            $sess = Yii::$app->session;
            if ($model->save()) {
                $sess->setFlash('success', Yii::t('user', 'The password was reset successfully. You can proceed to login with your new password.'));
                $action = $this->getConfig('actionSettings', Module::ACTION_LOGIN);
                return $this->redirect([$action]);
            } else {
                $sess->setFlash('error', Yii::t('user', 'Could not reset the password. Please try again later.'));
            }
        }
        return $this->render('reset', [
            'model' => $model,
        ]);
    }

    /**
     * Change email.
     *
     * @return mixed
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
            $sess = Yii::$app->session;
            if ($model->save()) {
                $sess->setFlash('success', Yii::t('user', 'The email address was changed successfully.'));
                $action = $this->getConfig('actionSettings', Module::ACTION_PROFILE_INDEX);
                return $this->redirect([$action]);
            } else {
                $sess->setFlash('error', Yii::t('user', 'Could not confirm the new email address. Please try again later.'));
            }
        }
        return $this->render('newemail', [
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
        if ($type !== 'activation' && $type !== 'reset' && $type !== 'email_change') {
            return null;
        }
        $class = $this->getConfig('modelSettings', Module::MODEL_USER);
        $attribute = "{$type}_key";
        return $class::findByKey($attribute, $key);
    }

    /**
     * Locks the user account
     *
     * @param Model $user the user model
     * @param string $msg the flash message to be displayed
     * @param string $link the reset link
     */
    protected function lockAccount($user, $msg)
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout(true);
        }
        if ($user !== null) {
            $user->scenario = Module::SCN_LOCKED;
            $user->save();
        }
        Yii::$app->session->setFlash('error', $msg);
        return $this->render(Module::SCN_LOCKED, ['user' => $user]);
    }

}