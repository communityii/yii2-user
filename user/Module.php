<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\Module;

use Yii;

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

	// the major user interface forms (the view names)
	const FORM_LOGIN = 'login';
	const FORM_REGISTRATION = 'register';
	const FORM_ACTIVATION = 'activation';
	const FORM_RECOVERY = 'recovery';
	const FORM_CHANGE_PASSWORD = 'change-password';
	const FORM_EDIT_PROFILE = 'edit-profile';

	// the rbac integration settings
	const RBAC_SIMPLE = 1;
	const RBAC_PHP = 2;
	const RBAC_DB = 3;

	/**
	 * @var array the login settings for the module. The following options can be set:
	 * - loginType: integer, whether users can login with their username, email address, or both.
	 *   Defaults to `Module::LOGIN_BOTH`.
	 * - rememberMeDuration: integer, the duration in seconds for which user will remain logged in on his/her client
	 *   using cookies. Defaults to 3600*24*30 seconds (30 days).
	 * - loginRedirectUrl: string|array, the default url to redirect after login. Normally the last return
	 *   url will be used. This setting will only be used for a new login instance.
	 * - logoutRedirectUrl: string|array, the default url to redirect after logout.
	 */
	public $loginSettings = [
		'loginType' => self::LOGIN_BOTH,
		'rememberMeDuration' => 2592000,
		'loginRedirectUrl' => '',
		'logoutRedirectUrl' => '',
	];

	/**
	 * @var array the settings for the password in the module. The following options can be set"
	 * - validateStrength: array|boolean, the list of forms where password strength will be validated. If
	 *   set to `false` or an empty array, no strength will be validated. The strength will be validated
	 *   using `\kartik\password\StrengthValidator`. Defaults to `[Module::FORM_REGISTRATION, Module::FORM_CHANGE_PASSWORD]`.
	 * - strengthRules: array, the strength validation rules as required by `\kartik\password\StrengthValidator`
	 * - strengthMeter: array|boolean, the list of forms where password strength meter will be displayed.
	 *   If set to `false` or an empty array, no strength meter will be displayed.  Defaults to
	 *   `[Module::FORM_REGISTRATION, Module::FORM_CHANGE_PASSWORD]`.
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
	public $passwordSettings = [
		'validateStrength' => [self::FORM_REGISTRATION, self::FORM_CHANGE_PASSWORD],
		'strengthRules' => [],
		'strengthMeter' => [self::FORM_REGISTRATION, self::FORM_CHANGE_PASSWORD],
		'activationKeyExpiry' => 172800,
		'resetKeyExpiry' => 172800,
		'passwordExpiry' => false,
		'wrongAttempts' => false,
		'enableRecovery' => true
	];

	/**
	 * @var array the registration settings for the module. The following options can be set:
	 * - enabled: bool, whether the registration is enabled for the module. Defaults to `true`. If set
	 *   to `false`, admins will need to create users. All the other registration settings will
	 *   be skipped if this is set to `false`.
	 * - showCaptcha: bool, whether to display captcha for registration. Defaults to `true`.
	 * - autoActivate: bool, whether account is automatically activated after registration. If set to
	 *   `false`, the user will need to complete activation before login. Defaults to `false`.
	 */
	public $registrationSettings = [
		'enabled' => true,
		'showCaptcha' => true,
		'autoActivate' => true
	];

	/**
	 * @var array the user notification settings for the module. Currently, only email notifications
	 * using Yii Swiftmail extension is supported. The following options can be set:
	 * - enabled: bool, whether the notifications are enabled for the module. Defaults to `true`. If set
	 *   to `false`, no notifications will be triggered for users.
	 * - viewPath: string, the path for notification email templates.
	 * - activation: array, the settings for the activation notification.
	 * - recovery: array, the settings for the recovery notification.
	 */
	public $notificationSettings = [
		'enabled' => true,
		'viewPath' => '@communityii/user/views/mail',
		'activation' => [
			'enabled' => true,
			'sentFrom' => '',
			'replyTo' => '',
			'template' => 'activation'
		],
		'recovery' => [
			'enabled' => true,
			'sentFrom' => '',
			'replyTo' => '',
			'template' => 'recovery'
		],
	];

	/**
	 * @var array the social authorization settings for the module. The following options should be set:
	 * - enabled: bool, whether the social authorization is enabled for the module. Defaults to `true`. If set
	 *   to `false`, the remote authentication through social providers will be disabled.
	 * - providers: array, the social provider configuration for remote authentication.
	 * - refreshAttributes: array, the attributes that will be automatically refreshed in the UserProfile,
	 *   based on the user consent, after social authentication. The 'email' field will be updated in the
	 *   base user table.
	 */
	public $socialAuthSettings = [
		'enabled' => true,
		'providers' => [
			'facebook' => [],
			'google' => []
		],
		'refreshAttributes' => [
			'profile_name',
			'email'
		],
	];

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
	public $avatarSettings = [
		'enabled' => true,
		'uploadSettings' => [
			'registration' => false,
			'profile' => true,
			'allowedTypes' => '.jpg, .gif, .png',
			'maxSize' => 2097152
		],
		'linkSocial' => true
	];

	/**
	 * @var array the settings for rbac. Contains
	 * - enabled: bool, whether the rbac integration is enabled for the module. Defaults to `true`.
	 * - `type`: string, the type of rbac. Defaults to RBAC_SIMPLE.
	 */
	public $rbacSettings = [
		'enabled' => true,
		'type' => self::RBAC_SIMPLE,
		'config' => [
			'class' => '\communityii\rbac\SimpleRBAC',
		]
	];


	/**
	 * @var array the global widget settings for each form (also available as a widget).
	 * Check each widget documentation for details. Few default settings:
	 * - `type`: the Bootstrap form orientation - one of `vertical`, `horizontal`, or `inline`.
	 */
	public $widgetSettings = [
		self::FORM_LOGIN => ['type' => 'vertical'],
		self::FORM_REGISTRATION => ['type' => 'horizontal'],
		self::FORM_ACTIVATION => ['type' => 'inline'],
		self::FORM_RECOVERY => ['type' => 'inline'],
		self::FORM_CHANGE_PASSWORD => ['type' => 'vertical'],
		self::FORM_EDIT_PROFILE => ['type' => 'vertical'],
	];

	/**
	 * @var array the list of admins/superusers.
	 */
	public $admins = [];

	/**
	 * @var array the the internalization configuration for
	 * this module
	 */
	public $i18n;

	public function init()
	{
		parent::init();
		Yii::setAlias('@usermodule', dirname(__FILE__));
		if (empty($this->i18n)) {
			$this->i18n = [
				'class' => 'yii\i18n\PhpMessageSource',
				'basePath' => '@usermodule/messages',
				'forceTranslation' => true
			];
		}
		Yii::$app->i18n->translations['usermodule'] = $this->i18n;
	}

}