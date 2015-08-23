<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use comyii\user\models\User;
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

    // the major user interfaces (forms/widgets) (some of these also mirror as scenario names in the User model)
    const UI_ACCESS = 'access';
    const UI_INSTALL = 'install';
    const UI_LOGIN = 'login';
    const UI_REGISTER = 'register';
    const UI_ACTIVATE = 'activate';
    const UI_RESET = 'reset';
    const UI_RECOVERY = 'recovery';
    const UI_LOCKED = 'locked';
    const UI_PROFILE = 'profile';
    const UI_ADMIN = 'admin';

    // the list of account actions
    const ACTION_LOGIN = 1;             // login as new user
    const ACTION_LOGOUT = 2;            // logout of account
    const ACTION_REGISTER = 3;          // new account registration
    const ACTION_ACTIVATE = 4;          // account activation
    const ACTION_RESET = 5;             // account password reset
    const ACTION_CHANGE = 6;            // account password change
    const ACTION_RECOVERY = 7;          // account recovery
    const ACTION_SOCIAL_AUTH = 20;      // social auth & login

    // the list of profile actions
    const ACTION_PROFILE_VIEW = 50;     // profile view
    const ACTION_PROFILE_LIST = 51;     // user listing
    const ACTION_PROFILE_EDIT = 52;     // profile update
    const ACTION_PROFILE_UPLOAD = 53;   // avatar image upload

    // the list of admin actions
    const ACTION_ADMIN_LIST = 100;      // user listing
    const ACTION_ADMIN_VIEW = 101;      // user view
    const ACTION_ADMIN_EDIT = 102;      // user edit
    const ACTION_ADMIN_BAN = 103;       // user ban
    const ACTION_ADMIN_UNBAN = 104;     // user unban

    // the mail delivery settings
    const ENQUEUE_ONLY = 1;
    const MAIL_ONLY = 2;
    const ENQUEUE_AND_MAIL = 3;

    /**
     * @var string code for accessing the user install configuration screen. You will need to
     * enter this for setting up the superuser for a new module install. If no value is set here,
     * and no superuser is set in the database, an exception will be raised on accessing the frontend.
     */
    public $installAccessCode;

    /**
     * @var Closure an anonymous function that will return current timestamp
     * for populating the timestamp fields. Defaults to
     * `function() { return date("Y-m-d H:i:s"); }`
     */
    public $now;

    /**
     * @var array the action settings for the module. The keys will be one of the `Module::ACTION_` constants
     * and the value will be the url/route for the specified action.
     * @see `setConfig()` method for the default settings
     */
    public $actionSettings = [];
    

    /**
     * @var array the view layout to use for each action in the module. The keys will be one 
     * of the `Module::ACTION_` constants and the value will be the view layout location. 
     * @see `setConfig()` method for the default settings
     */
    public $layoutSettings = [];

    /**
     * @var array the login settings for the module. The following options can be set:
     * - loginType: integer, whether users can login with their username, email address, or both.
     *   Defaults to `Module::LOGIN_BOTH`.
     * - rememberMeDuration: integer, the duration in seconds for which user will remain logged in on his/her client
     *   using cookies. Defaults to 3600*24*1 seconds (30 days).
     * - loginRedirectUrl: string|array, the default url to redirect after login. Normally the last return
     *   url will be used. This setting will only be used if no return url is found.
     * - logoutRedirectUrl: string|array, the default url to redirect after logout. If not set, it will redirect
     *   to home page.
     * @see `setConfig()` method for the default settings
     */
    public $loginSettings = [];

    /**
     * @var array the settings for the password in the module. The following options can be set"
     * - validateStrength: array|boolean, the list of forms where password strength will be validated. If
     *   set to `false` or an empty array, no strength will be validated. The strength will be validated
     *   using `\kartik\password\StrengthValidator`. Defaults to `[Module::UI_REGISTER, Module::UI_RESET]`.
     * - strengthRules: array, the strength validation rules as required by `\kartik\password\StrengthValidator`
     * - strengthMeter: array|boolean, the list of forms where password strength meter will be displayed.
     *   If set to `false` or an empty array, no strength meter will be displayed.  Defaults to
     *   `[Module::UI_REGISTER, Module::UI_RESET]`.
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
     * @see `setConfig()` method for the default settings
     */
    public $passwordSettings = [];

    /**
     * @var array the registration settings for the module. The following options can be set:
     * - enabled: bool, whether the registration is enabled for the module. Defaults to `true`. If set
     *   to `false`, admins will need to create users. All the other registration settings will
     *   be skipped if this is set to `false`.
     * - captcha: array|bool, the settings for the captcha. If set to `false`, no captcha will be displayed.
     *   Defaults to `[]`.
     * - autoActivate: bool, whether account is automatically activated after registration. If set to
     *   `false`, the user will need to complete activation before login. Defaults to `false`.
     * - userNameRules: array, the yii\validators\StringValidator rules for the username. Defaults to
     *   `['min' => 4, 'max' => 30]`.
     * - userNamePattern: string, the regular expression to match for characters allowed in the username.
     *   Defaults to `/^[A-Za-z0-9_\-]+$/u`.
     * - userNameValidMsg: string, the error message to display if the username pattern validation fails.
     *   Defaults to `"{attribute} can contain only letters, numbers, hyphen, and underscore."`.
     * @see `setConfig()` method for the default settings
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
     * - mailDelivery: integer, one of the mailDelivery options `Module::ENQUEUE_ONLY`, `Module::MAIL_ONLY`,
     *   or `Module::ENQUEUE_AND_MAIL`. Defaults to `Module::ENQUEUE_AND_MAIL`,
     * @see `setConfig()` method for the default settings
     */
    public $notificationSettings = [];

    /**
     * @var array the social authorization settings for the module. The following options should be set:
     * - enabled: bool, whether the social authorization is enabled for the module. Defaults to `true`. If set
     *   to `false`, the remote authentication through social providers will be disabled.
     * - refreshAttributes: array, the attributes that will be automatically refreshed in the UserProfile,
     *   based on the user consent, after social authentication. The 'email' field will be updated in the
     *   base user table.
     * @see `setConfig()` method for the default settings
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
     * @see `setConfig()` method for the default settings
     */
    public $avatarSettings = [];


    /**
     * @var array the global widget settings for each form (also available as a widget).
     * Check each widget documentation for details. Few default settings:
     * - `type`: the Bootstrap form orientation - one of `vertical`, `horizontal`, or `inline`.
     * @see `setConfig()` method for the default settings
     */
    public $widgetSettings = [];

    /**
     * @var the messages displayed to the user for various actions
     * @see `setConfig()` method for the default settings
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
        if (empty($this->i18n)) {
            $this->i18n = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vendor/communityii/user/messages',
                'forceTranslation' => true
            ];
        }
        Yii::$app->i18n->translations['user'] = $this->i18n;
        $this->setConfig();
        Yii::setAlias('@user', dirname(__FILE__));
    }

    /**
     * Return errors as bulleted list for model
     * @param $model
     * @return string
     */
    public static function showErrors($model) {
        $errors = [];
        foreach($model->getAttributes() as $attribute => $setting) {
            $error = $model->getFirstError($attribute);
            if (trim($error) != null) {
                $errors[] = $error;
            }
        }
        return '<ul><li>' . implode("</li>\n<li>", $errors) . '</li></ul>';
    }

    /**
     * Sets the module configuration defaults
     */
    public function setConfig()
    {
        if (empty($this->now) || !$this->now instanceof \Closure) {
            $this->now = function () {
                return date('Y-m-d H:i:s');
            };
        }
        $this->actionSettings += [
            // the list of account actions
            self::ACTION_LOGIN => 'account/login',
            self::ACTION_LOGOUT => 'account/logout',
            self::ACTION_REGISTER => 'account/register',
            self::ACTION_ACTIVATE => 'account/activate',
            self::ACTION_RESET => 'account/reset',
            self::ACTION_CHANGE => 'account/change',
            self::ACTION_RECOVERY => 'account/recovery',
            // the list of social actions
            self::ACTION_SOCIAL_AUTH => 'account/auth',
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
        $this->layoutSettings += [
            // layouts for the various account actions
            self::ACTION_LOGIN => 'install',
            self::ACTION_LOGOUT => 'install',
            self::ACTION_REGISTER => 'install',
            self::ACTION_ACTIVATE => 'install',
            self::ACTION_RESET => 'install',
            self::ACTION_CHANGE => 'install',
            self::ACTION_RECOVERY => 'install',
            // the list of social actions
            self::ACTION_SOCIAL_AUTH => 'social/login',
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
            'validateStrength' => [self::UI_INSTALL, self::UI_REGISTER, Module::UI_RESET],
            'strengthRules' => [
                'min' => 8,
                'upper' => 1,
                'lower' => 1,
                'digit' => 1,
                'special' => 0,
                'hasUser' => true,
                'hasEmail' => true
            ],
            'strengthMeter' => [self::UI_INSTALL, self::UI_REGISTER, Module::UI_RESET],
            'activationKeyExpiry' => 172800,
            'resetKeyExpiry' => 172800,
            'passwordExpiry' => false,
            'wrongAttempts' => false,
            'enableRecovery' => true
        ];
        $this->registrationSettings += [
            'enabled' => true,
            'captcha' => [],
            'autoActivate' => false,
            'userNameRules' => ['min' => 4, 'max' => 30],
            'userNamePattern' => '/^[A-Za-z0-9_-]+$/u',
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
            'mailDelivery' => self::ENQUEUE_AND_MAIL
        ];
        $this->socialAuthSettings += [
            'enabled' => true,
            'refreshAttributes' => [
                'display_name',
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
        $this->widgetSettings += [
            self::UI_LOGIN => ['type' => 'vertical'],
            self::UI_REGISTER => ['type' => 'horizontal'],
            self::UI_ACTIVATE => ['type' => 'inline'],
            self::UI_RECOVERY => ['type' => 'inline'],
            self::UI_RESET => ['type' => 'vertical'],
            self::UI_PROFILE => ['type' => 'vertical'],
            self::UI_ADMIN => ['type' => 'vertical'],
        ];
        $this->initMessages();
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
            throw new InvalidConfigException("The module 'user' was not found . Ensure you have setup the 'user' module in your Yii configuration file.");
        }
    }
    
    /**
     * Initialize messages for the module
     */
    public function initMessages() {
        $this->messages += [
            'here' => 'here',
            'install-error' => 'Error creating the superuser. Fix the following errors:<br>{errors}',
            'install-success' => 'User module successfully installed! You have been automatically logged in as the superuser (username: <b>{username}</b>).',
            'install-warning' => 'You should now remove the <code>installAccessCode</code> setting from user module configuration for better security.',
            'install-invalid-access' => 'The installation access code entered is incorrect.',
            'install-invalid-usercomp' => 'You have not setup a valid class for your user component in your application configuration file. ' .
                'The class must extend {classValid}. Class currently set: {classSet}.',
            'registration-active' => 'You have been successfully registered and logged in as <b>{username}</b>.',
            'pending-activation' => 'Instructions for activating your account has been sent to your email <b>{email}</b>.',
            'pending-activation-error' => 'Could not send activation instructions to your email <b>{email}</b>. Contact the system administrator.',
            'password-expired' => 'Your password has expired. You may reset your password by clicking {link}.',
            'account-locked' => 'Your account has been locked due to multiple wrong password attempts. You may reset and activate your account by clicking {link}.',
            'login-title' => 'Please sign in',
            'social-auth-title' => 'Or login using',
            'social-email-exists' => 'User with the same email as in <b>{client}</b> account already exists but is not linked to it. Login using email first to link it.',  
            'social-auth-success-new' => 'Successfully authenticated <b>{client}</b> account.',
            'social-auth-success-curr' => 'Successfully authenticated <b>{client}</b> account for <b>{user}</b>.',
            'social-auth-error-new' => 'Error while authenticating <b>{client}</b> account. {errors}',
            'social-auth-error-curr' => 'Error while authenticating <b>{client}</b> account for <b>{user}</b>. {errors}',
            'user-details-title' => 'User Details',
            'user-id-info-title' => 'Identification Information',
            'user-log-info-title' => 'User Log Information',
            'user-hidden-info-title' => 'Hidden Information',
            'user-details-saved' => 'Successfully saved details for <b>{user}</b> with ID <b>{id}</b>.',
            'login-banned' => 'User has been banned.',
            'login-invalid' => 'Invalid username or password.',
            'tooltip-change-password' => 'Change password for the user',
            'tooltip-reset-password' => 'Reset password for the user',
            'tooltip-forgot-password' => 'Click here to reset your lost password',
            'label-forgot-password' => 'Forgot password?',
            'label-reset-password' => 'Reset password',
            'label-change-password' => 'Change password',
            'label-password-actions' => 'Password Actions',
            'label-id' => 'ID',
            'label-username' => 'Username',
            'label-password' => 'Password',
            'label-password-hash' => 'Password Hash',
            'label-password-new' => 'New Password',
            'label-password-confirm' => 'Confirm Password',
            'label-email' => 'Email',
            'label-user-or-email' => 'Username or Email',
            'label-remember-me' => 'Remember Me',
            'label-status' => 'Status',
            'label-created-on' => 'Created On',
            'label-updated-on' => 'Updated On',
            'label-auth-key' => 'Auth Key',
            'label-activation-key' => 'Activation Key',
            'label-reset-key' => 'Reset Key',
            'label-last-login-ip' => 'Last Login IP',
            'label-last-login-on' => 'Last Login On',
            'label-password-reset-on' => 'Password Reset On',
            'label-password-fail-attempts' => 'Password Fail Attempts',
            'status-superuser' => 'Superuser',
            'status-pending' => 'Pending',
            'status-active' => 'Active',
            'status-inactive' => 'Inactive',
            'status-banned' => 'Banned',
            'label-install-access-code' => 'Installation Access Code',
            'hint-install-access-code' => 'Enter the installation access code as setup in your module configuration.',
            'hint-install-username' => 'Select an username for the superuser',
            'hint-install-password' => 'Select a password for the superuser',
            'hint-install-password-confirm' => 'Reconfirm the superuser password',
            'hint-install-email' => 'Enter a valid email for the superuser',
        ];
    }
    
    /**
     * Fetch the message for a message identifier
     * @param string $id the message identifier
     * @param array $params the message parameters
     * @return string the parsed message
     */
    public function message($id, $params = []) {
        if (!isset($this->messages[$id])) {
            return null;
        }
        return Yii::t('user', $this->messages[$id], $params);
    }
    
    /**
     * Sets a message flash
     * @param string $cat the message flash category 
     * @param string $id the message identifier
     * @param array $params the message parameters
     */
    public function setFlash($cat, $id, $params = []) {
        $message = $this->message($id, $params);
        if ($message) { 
            Yii::$app->session->setFlash($cat, $message);
        }
    }

    /**
     * Superuser already exists in the database?
     * @return bool
     */
    public function hasSuperUser() {
        return count(User::find()->superuser()->all()) > 0;
    }
    
    /**
     * Generate action button
     */
    public function actionButton($action, $msgId, $icon, $options = ['class' => 'btn-xs'], $titleId = '', $prefix = 'glyphicon glyphicon-')
    {
        Html::addCssClass($options, 'btn');
        if (!empty($titleId)) {
            $options['title'] = $this->message($titleId);
        }
        $label = "<i class='{$prefix}{$icon}'></i> " . $this->message($msgId);
        return Html::a($label, ArrayHelper::getValue($this->actionSettings, [$action], '#'), $options);
    }
}