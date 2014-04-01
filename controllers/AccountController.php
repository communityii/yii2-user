<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\VerbFilter;
use communityii\user\Module;
use communityii\user\models\LoginForm;
use communityii\user\models\User;

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
				'class' => \yii\web\AccessControl::className(),
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
		$captcha = $this->getConfig('registrationSettings', 'captcha');
		if ($captcha === false || !is_array($captcha)) {
			return [];
		}
		return [
			'captcha' => ['class' => 'yii\captcha\CaptchaAction'] + $captcha
		];
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

		$model = new LoginForm();

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$user = $model->getUser();
			$link = Html::a(Yii::t('user', 'here'), Module::ACTION_RESET);
			if ($user->status == User::STATUS_INACTIVE) {
				$msg = ($user->isAccountLocked()) ? Module::MSG_ACCOUNT_LOCKED : Module::MSG_PASSWORD_EXPIRED;
				Yii::$app->session->setFlash('error', Yii::t('user', $msg, ['resetLink' => $link]));
			} elseif ($user->isAccountLocked()) {
				$user->saveStatus(User::STATUS_INACTIVE);
				Yii::$app->session->setFlash('error', Yii::t('user', Module::MSG_ACCOUNT_LOCKED, ['resetLink' => $link]));
			} elseif ($user->isPasswordExpired()) {
				$user->saveStatus(User::STATUS_INACTIVE);
				Yii::$app->session->setFlash('error', Yii::t('user', Module::MSG_PASSWORD_EXPIRED, ['resetLink' => $link]));
			} elseif ($model->login($user)) {
				$user->setLastLogin();
				return $this->goBack($url);
			}
		}
		return $this->render(Module::FORM_LOGIN, ['model' => $model]);
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
		$config = $this->module->registrationSettings;
		if (!$config['enabled']) {
			return $this->goBack();
		}
		$model = new User(['scenario' => Module::FORM_REGISTER]);
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($config['autoActivate']) {
				$model->setStatus(User::STATUS_ACTIVE);
				$model->save();
				Yii::$app->session->setFlash("success", Yii::t('user', Module::MSG_REGISTRATION_ACTIVE, ['username' => $model->username]));
				return $this->goHome();
			} else {
				$model->save();
				if ($model->sendEmail('activation')) {
					Yii::$app->session->setFlash("success", Yii::t('user', Module::MSG_PENDING_ACTIVATION, ['email' => $model->email]));
				} else {
					Yii::$app->session->setFlash("warning", Yii::t('user', Module::MSG_PENDING_ACTIVATION_ERR, ['email' => $model->email]));
				}
			}
		}
		return $this->render(Module::FORM_REGISTER, ['model' => $model, 'config' => $config]);
	}

}