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
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\authclient\AuthAction;
use comyii\user\Module;
use comyii\user\models\LoginForm;
use comyii\user\models\SocialAuth;
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
                'only' => ['logout', 'register'],
                'rules' => [
                    [
                        'actions' => ['register'],
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
                    //'logout' => ['post'],
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
        $captcha = $this->getConfig('registrationSettings', 'captcha');
        if ($captcha === false || !is_array($captcha)) {
            return [];
        }
        return [
            'captcha' => ['class' => 'yii\captcha\CaptchaAction'] + $captcha,
            'auth' => [
                'class' => AuthAction::classname(),
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
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

        /** @var Auth $auth */
        $auth = SocialAuth::find()->where([
            'source' => $clientId,
            'source_id' => $attributes['id'],
        ])->one();
        
        if (Yii::$app->user->isGuest) {
            $m = $this->module;
            if ($auth) { // login
                $user = $auth->user;
                Yii::$app->user->login($user);
            } else { // signup
                if (isset($attributes['email']) && isset($attributes['username']) && 
                    User::find()->where(['email' => $attributes['email']])->exists()) {
                    $m->setFlash('error', 'social-email-exists', ['client' => $clientTitle]);
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
                    $user = new User([
                        'username' => $attributes['login'],
                        'email' => $attributes['email'],
                        'password' => $password,
                    ]);
                    $user->generateAuthKey();
                    $user->generateResetKey();
                    $success = false;
                    $transaction = $user->getDb()->beginTransaction();
                    if ($user->save()) {
                        $auth = new SocialAuth([
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
                        $m->setFlash('error', 'social-auth-error-new', [
                            'client' => $clientTitle,
                            'errors' => '<pre>' . print_r($user->getErrors(), true) . '</pre>'
                        ]);
                    } else {
                        $m->setFlash('success', 'social-auth-success-new', ['client' => $clientTitle]);
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $id = Yii::$app->user->id;
                $user = User::findOne($id);
                $auth = new SocialAuth([
                    'user_id' => $id,
                    'source' => $clientId,
                    'source_id' => $attributes['id'],
                ]);
                if ($auth->save()) {
                    $m->setFlash('success', 'social-auth-success-curr', [
                        'client' => $clientTitle,
                        'user' => $user->username
                    ]);
                } else {
                    $m->setFlash('error', 'social-auth-error-curr', [
                        'client' => $clientTitle,
                        'user' => $user->username,
                        'errors' => '<pre>' . print_r($auth->getErrors(), true) . '</pre>'
                    ]);
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
        $hasSocialAuth = $this->getConfig('socialAuthSettings', 'enabled', false);
        $authAction = $this->getConfig('actionSettings', Module::ACTION_SOCIAL_AUTH);
        if ($hasSocialAuth && empty(Yii::$app->authClientCollection) && empty(Yii::$app->authClientCollection->clients)) {
            throw new InvalidConfigException("You must setup the `authClientCollection` component and its `clients` in your app configuration file.");
        }
        $this->layout = $this->getConfig('layoutSettings', Module::ACTION_LOGIN);
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $model->getUser();
            $link = Html::a($this->module->message('here'), Module::ACTION_RESET);
            if ($user->status === User::STATUS_INACTIVE) {
                $msg = ($user->isPasswordExpired()) ? 'password-expired' : 'account-locked';
                return $this->lockAccount(null, $msg, $link);
            } elseif ($user->isPasswordExpired()) {
                return $this->lockAccount($user, 'password-expired', $link);
            } elseif ($user->isAccountLocked()) {
                return $this->lockAccount($user, 'account-locked', $link);
            } elseif ($model->login($user)) {
                $user->setLastLogin();
                return $this->goBack($url);
            }
        }
        return $this->render(Module::UI_LOGIN, [
            'model' => $model, 
            'hasSocialAuth' => $hasSocialAuth,
            'authAction' => $authAction,
            'loginTitle' => $this->module->message('login-title'),
            'authTitle' => $this->module->message('social-auth-title')
        ]);
    }

    /**
     * Locks the user account
     *
     * @param Model $user the user model
     * @param string $msg the flash message to be displayed
     * @param string $link the reset link
     */
    protected function lockAccount($user, $msg, $link)
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout(true);
        }
        if ($user !== null) {
            $user->scenario = Module::UI_LOCKED;
            $user->save();
        }
        $this->module->setFlash('error', $msg, ['resetLink' => $link]);
        return $this->render(Module::UI_LOCKED, ['user' => $user]);
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
        $model = new User(['scenario' => Module::UI_REGISTER]);
        $m = $this->module;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($config['autoActivate']) {
                $model->setStatus(User::STATUS_ACTIVE);
                $model->save();
                $m->setFlash('success', 'registration-active', ['username' => $model->username]);
                return $this->goHome();
            } else {
                $model->save();
                if ($model->sendEmail('activation')) {
                    $m->setFlash('success', 'pending-activation', ['email' => $model->email]);
                } else {
                    $m->setFlash('warning', 'pending-activation-error', ['email' => $model->email]);
                }
            }
        }
        return $this->render(Module::UI_REGISTER, ['model' => $model, 'config' => $config]);
    }

}