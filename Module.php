<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\Module;

use Yii;
use communityii\user\models\User;
use yii\base\InvalidConfigException;

/**
 * User module with inbuilt social authentication for Yii framework 2.0.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Module extends \yii\base\Module
{
	// the valid types of login methods
	const LOGIN_USERNAME = 1;
	const LOGIN_EMAIL = 2;
	const LOGIN_BOTH = 3;

	// the major user interface forms (some of these also mirror as scenario names in the User model)
	const FORM_LOGIN = 'login';
	const FORM_REGISTER = 'register';
	const FORM_ACTIVATE = 'activate';
	const FORM_RESET = 'reset';
	const FORM_RECOVERY = 'recovery';
	const FORM_INACTIVATE = 'inactivate';
	const FORM_PROFILE = 'profile';
	const FORM_ADMIN = 'admin';

	// the rbac integration settings
	const RBAC_SIMPLE = 1;
	const RBAC_PHP = 2;
	const RBAC_DB = 3;

	// the list of account actions
	const ACTION_LOGIN = 1; // login as new user
	const ACTION_LOGOUT = 2; // logout of account
	const ACTION_REGISTER = 3; // new account registration
	const ACTION_ACTIVATE = 4; // account activation
	const ACTION_RESET = 5; // account password reset
	const ACTION_RECOVERY = 6; // account recovery

	// the list of social actions
	const ACTION_SOCIAL_LOGIN = 20; // social auth & login

	// the list of profile actions
	const ACTION_PROFILE_VIEW = 50; // profile view
	const ACTION_PROFILE_LIST = 51; // user listing
	const ACTION_PROFILE_EDIT = 52; // profile update
	const ACTION_PROFILE_UPLOAD = 53; // avatar image upload

	// the list of admin actions
	const ACTION_ADMIN_LIST = 100; // user listing
	const ACTION_ADMIN_VIEW = 101; // user view
	const ACTION_ADMIN_EDIT = 102; // user edit
	const ACTION_ADMIN_BAN = 103; // user ban
	const ACTION_ADMIN_UNBAN = 104; // user unban

	// the list of module messages
	const MSG_REGISTRATION_ACTIVE = 200;
	const MSG_PENDING_ACTIVATION = 201;
	const MSG_PENDING_ACTIVATION_ERR = 202;
	const MSG_PASSWORD_EXPIRED = 203;
	const MSG_ACCOUNT_LOCKED = 204;

	/**
	 * @var array the action settings for the module. The keys will be one of the `Module::ACTION_` constants
	 * and the value will be the url/route for the specified action.
	 */
	public $actionSettings = [];

	/**
	 * @var array the login settings for the module. The following options can be set:
	 * - loginType: integer, whether users can login with their username, email address, or both.
	 *   Defaults to `Module::LOGIN_BOTH`.
	 * - rememberMeDuration: integer, the duration in seconds for which user will remain logged in on his/her client
	 *   using cookies. Defaults to 3600*24*30 seconds (30 days).
	 * - loginRedirectUrl: string|array, the default url to redirect after login. Normally the last return
	 *   url will be used. This setting will only be used if no return url is found.
	 * - logoutRedirectUrl: string|array, the default url to redirect after logout. If not set, it will redirect
	 *   to home page.
	 */
	public $loginSettings = [];

	/**
	 * @var array the settings for the password in the module. The following options can be set"
	 * - validateStrength: array|boolean, the list of forms where password strength will be validated. If
	 *   set to `false` or an empty array, no strength will be validated. The strength will be validated
	 *   using `\kartik\password\StrengthValidator`. Defaults to `[Module::FORM_REGISTER, Module::FORM_RESET]`.
	 * - strengthRules: array, the strength validation rules as required by `\kartik\password\StrengthValidator`
	 * - strengthMeter: array|boolean, the list of forms where password strength meter will be displayed.
	 *   If set to `false` or an empty array, no strength meter will be displayed.  Defaults to
	 *   `[Module::FORM_REGISTER, Module::FORM_RESET]`.
	 * - activationKeyExpiry: integer|bool, the time in seconds after which the account activation key/token will expire.
	 *   Defaults to 3600*24*2 seconds (2 days). If set to `0` or `false`, the key never expires.
	 * - resetKeyExpiry: integer|bool, the time in seconds after which the password reset key/token will expire.
	 *   Defaults to 3600*24*2 seconds (2 days). If set to `0` or `false`, the key never expires.
	 * - passwordExpiry: integer|bool, the timeout in seconds after which user is required to reset his password
	 *   after logging in. Defaults to `false`. If set to `0` or `false`, the password never expires.
	 * - wrongAttempts: integer|bool, the number of consecutive wrong password type attempts, at login, after which
	 *   the account is inactivated and needs to be reset. Defaults to `false`. If set to `0` or `false`, the account
	 *   is never inactivated after any wrong password attempts.
	 * - enableRecovery: bool, whether password recovery is permitted. If set to `true`, users will be given an option
	 *   to reset/recover a lost password. Defaults to `true`.
	 */
	public $passwordSettings = [];

	/**
	 * @var array the registration settings for the module. The following options can be set:
	 * - enabled: bool, whether the registration is enabled for the module. Defaults to `true`. If set
	 *   to `false`, admins will need to create users. All the other registration settings will
	 *   be skipped if this is set to `false`.
	 * - captcha: array|bool, the settings for the captcha. If set to `false`, no captcha will be displayed.
	 *   Defaults to `['backColor' => 0xFFFFFF, 'testLimit' => 0]`.
	 * - autoActivate: bool, whether account is automatically activated after registration. If set to
	 *   `false`, the user will need to complete activation before login. Defaults to `false`.
	 * - userNameLength: integer, the minimum length for the username field. Defaults to 4.
	 * - userNamePattern: string, the regular expression to match for characters allowed in the username.
	 *   Defaults to `/^[A-Za-z0-9_\-]+$/u`.
	 * - userNameValidMsg: string, the error message to display if the username pattern validation fails.
	 *   Defaults to `"{attribute} can contain only letters, numbers, hyphen, and underscore."`.
	 */
	public $registrationSettings = [];

	/**
	 * @var array the user notification settings for the module. Currently, only email notifications
	 * using Yii Swiftmail extension is supported. The following options can be set:
	 * - enabled: bool, whether the notifications are enabled for the module. Defaults to `true`. If set
	 *   to `false`, no notifications will be triggered for users.
	 * - viewPath: string, the path for notification email templates.
	 * - activation: array, the settings for the activation notification.
	 * - recovery: array, the settings for the recovery notification.
	 */
	public $notificationSettings = [];

	/**
	 * @var array the social authorization settings for the module. The following options should be set:
	 * - enabled: bool, whether the social authorization is enabled for the module. Defaults to `true`. If set
	 *   to `false`, the remote authentication through social providers will be disabled.
	 * - providers: array, the social provider configuration for remote authentication.
	 * - refreshAttributes: array, the attributes that will be automatically refreshed in the UserProfile,
	 *   based on the user consent, after social authentication. The 'email' field will be updated in the
	 *   base user table.
	 */
	public $socialAuthSettings = [];

	/**
	 * @var array the settings for the user avatar.
	 * - enabled: bool, whether the user avatar is enabled for the module. Defaults to `true`.
	 * - uploadSettings: array, the settings for upload of the avatar
	 *   - registration: bool, whether avatar can be uploaded at registration. Defaults to `false`.
	 *   - profile: bool, whether avatar can be uploaded from user profile. Defaults to `true`.
	 *   - allowedTypes: string, the list of file types allowed for upload.
	 *   - maxSize: integer, the maximum size (bytes) allowed for the uploaded file.
	 * - linkSocial: bool, whether the avatar image can be linked with a social profile's avatar
	 *   based on user consent. Defaults to `true`.
	 */
	public $avatarSettings = [];

	/**
	 * @var array the settings for rbac. Contains
	 * - enabled: bool, whether the rbac integration is enabled for the module. Defaults to `true`.
	 * - `type`: string, the type of rbac. Defaults to RBAC_SIMPLE.
	 */
	public $rbacSettings = [];


	/**
	 * @var array the global widget settings for each form (also available as a widget).
	 * Check each widget documentation for details. Few default settings:
	 * - `type`: the Bootstrap form orientation - one of `vertical`, `horizontal`, or `inline`.
	 */
	public $widgetSettings = [];

	/**
	 * @var the messages displayed to the user for various actions
	 */
	public $messages = [];

	/**
	 * @var array the list of admins/superusers.
	 */
	public $admins = [];

	/**
	 * @var array the the internalization configuration for
	 * this module
	 */
	public $i18n;

	/**
	 * Initialize the module
	 */
	public function init()
	{
		parent::init();
		$this->setConfig();
		Yii::setAlias('@user', dirname(__FILE__));
		if (empty($this->i18n)) {
			$this->i18n = [
				'class' => 'yii\i18n\PhpMessageSource',
				'basePath' => '@user/messages',
				'forceTranslation' => true
			];
		}
		Yii::$app->i18n->translations['user'] = $this->i18n;
	}

	/**
	 * Sets the module configuration defaults
	 */
	public function setConfig()
	{
		$this->actionSettings += [
			// the list of account actions
			self::ACTION_LOGIN => 'account/login',
			self::ACTION_LOGOUT => 'account/logout',
			self::ACTION_REGISTER => 'account/register',
			self::ACTION_ACTIVATE => 'account/activate',
			self::ACTION_INACTIVATE => 'account/inactivate',
			self::ACTION_RESET => 'account/reset',
			self::ACTION_RECOVERY => 'account/recovery',
			// the list of social actions
			self::ACTION_SOCIAL_LOGIN => 'social/login',
			// the list of profile actions
			self::ACTION_PROFILE_VIEW => 'profile/view',
			self::ACTION_PROFILE_LIST => 'profile/index',
			self::ACTION_PROFILE_EDIT => 'profile/update',
			self::ACTION_PROFILE_UPLOAD => 'profile/upload',
			// the list of admin actions
			self::ACTION_ADMIN_LIST => 'admin/index',
			self::ACTION_ADMIN_VIEW => 'admin/view',
			self::ACTION_ADMIN_EDIT => 'admin/update',
			self::ACTION_ADMIN_BAN => 'admin/ban',
			self::ACTION_ADMIN_UNBAN => 'admin/unban',
		];
		$this->loginSettings += [
			'loginType' => self::LOGIN_BOTH,
			'rememberMeDuration' => 2592000
		];
		$this->passwordSettings += [
			'validateStrength' => [self::FORM_REGISTER, Module::FORM_RESET],
			'strengthRules' => [
				'min' => 8,
				'upper' => 1,
				'lower' => 1,
				'digit' => 1,
				'special' => 0,
				'hasUser' => true,
				'hasEmail' => true
			],
			'strengthMeter' => [self::FORM_REGISTER, Module::FORM_RESET],
			'activationKeyExpiry' => 172800,
			'resetKeyExpiry' => 172800,
			'passwordExpiry' => false,
			'wrongAttempts' => false,
			'enableRecovery' => true
		];
		$this->registrationSettings += [
			'enabled' => true,
			'captcha' => ['backColor' => 0xFFFFFF, 'testLimit' => 0],
			'autoActivate' => false,
			'userNameLength' => 4,
			'userNamePattern' => '/^[A-Za-z0-9_\-]+$/u',
			'userNameValidMsg' => Yii::t('user', '{attribute} can contain only letters, numbers, hyphen, and underscore.')
		];
		$appName = \Yii::$app->name;
		$supportEmail = isset(\Yii::$app->params['supportEmail']) ? \Yii::$app->params['supportEmail'] : 'nobody@support.com';
		$this->notificationSettings += [
			'enabled' => true,
			'viewPath' => '@communityii/user/views/mail',
			'activation' => [
				'enabled' => true,
				'fromEmail' => $supportEmail,
				'fromName' => Yii::t('user', '{appname} Robot', ['appname' => $appName]),
				'subject' => Yii::t('user', Yii::t('user', 'Account activation for {appname}', ['appname' => $appName])),
				'template' => 'activation'
			],
			'recovery' => [
				'enabled' => true,
				'fromEmail' => $supportEmail,
				'fromName' => Yii::t('user', '{appname} Robot', ['appname' => $appName]),
				'subject' => Yii::t('user', Yii::t('user', 'Account recovery for {appname}', ['appname' => $appName])),
				'template' => 'recovery'
			],
		];
		$this->socialAuthSettings += [
			'enabled' => true,
			'refreshAttributes' => [
				'profile_name',
				'email'
			],
		];
		$this->avatarSettings += [
			'enabled' => true,
			'uploadSettings' => [
				'registration' => false,
				'profile' => true,
				'allowedTypes' => '.jpg, .gif, .png',
				'maxSize' => 2097152
			],
			'linkSocial' => true
		];
		$this->rbacSettings += [
			'enabled' => true,
			'type' => self::RBAC_SIMPLE,
			'config' => [
				'class' => '\communityii\rbac\SimpleRBAC',
			]
		];
		$this->widgetSettings += [
			self::FORM_LOGIN => ['type' => 'vertical'],
			self::FORM_REGISTER => ['type' => 'horizontal'],
			self::FORM_ACTIVATION => ['type' => 'inline'],
			self::FORM_RECOVERY => ['type' => 'inline'],
			self::FORM_RESET => ['type' => 'vertical'],
			self::FORM_PROFILE => ['type' => 'vertical'],
			self::FORM_ADMIN => ['type' => 'vertical'],
		];
		$this->messages += [
			self::MSG_REGISTRATION_ACTIVE => "You have been successfully registered and logged in as '{username}'",
			self::MSG_PENDING_ACTIVATION => "Your registration form has been received. Instructions for activating your account has been sent to your email '{email}'.",
			self::MSG_PENDING_ACTIVATION_ERR => "Your registration form has been received. Activation instructions could not be sent to your email '{email}'. Contact the system administrator.",
			self::MSG_PASSWORD_EXPIRED => "Your password has expired. You may reset your password by clicking {here}.",
			self::MSG_ACCOUNT_LOCKED => "Your account has been locked due to multiple wrong password attempts. You may reset and activate your account by clicking {here}."
		];
	}

	/**
	 * Validate the module configuration
	 *
	 * @param Module $module the user module object
	 * @throws InvalidConfigException
	 */
	public static function validateConfig(&$module)
	{
		$module = Yii::$app->getModule('user');
		if ($module === null) {
			throw new InvalidConfigException("The module 'user' was not found . Ensure you have setup the 'user' module in your Yii configuration file . ");
		}
	}
}