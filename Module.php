<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\helpers\Html;
use comyii\user\models\User;

/**
 * User module with inbuilt social authentication for Yii framework 2.0.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Module extends \kartik\base\Module
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
    const UI_CHANGEPASS = 'password';
    const UI_RECOVERY = 'recovery';
    const UI_LOCKED = 'locked';
    const UI_PROFILE = 'profile';
    const UI_ADMIN = 'admin';
    const UI_NEWEMAIL = 'newemail';

    // the list of account actions
    const ACTION_LOGIN = 1;             // login as new user
    const ACTION_LOGOUT = 2;            // logout of account
    const ACTION_REGISTER = 3;          // new account registration
    const ACTION_ACTIVATE = 4;          // account activation
    const ACTION_RECOVERY = 5;          // account password recovery request
    const ACTION_RESET = 6;             // account password reset
    const ACTION_CAPTCHA = 7;           // account captcha for registration
    const ACTION_SOCIAL_AUTH = 15;      // social auth & login

    // the list of profile actions
    const ACTION_PROFILE_INDEX = 50;     // profile index
    const ACTION_PROFILE_MANAGE = 51;     // profile view
    const ACTION_PROFILE_EDIT = 52;     // profile update
    const ACTION_ACCOUNT_PASSWORD = 53; // profile password change
    const ACTION_AVATAR_UPLOAD = 54;    // profile image upload
    const ACTION_AVATAR_DELETE = 55;    // profile image delete

    // the list of admin actions
    const ACTION_ADMIN_LIST = 100;      // user listing
    const ACTION_ADMIN_VIEW = 101;      // user view
    const ACTION_ADMIN_EDIT = 102;      // user edit
    const ACTION_ADMIN_CREATE = 103;    // user creation (only for admin)
    const ACTION_ADMIN_RESET = 104;     // user password reset

    // the list of various action buttons
    const BTN_HOME = 'home';                        // back to home page
    const BTN_BACK = 'back';                        // back to previous page
    const BTN_RESET_FORM = 'reset-form';            // reset form button
    const BTN_SUBMIT_FORM = 'submit-form';          // submit button
    const BTN_SAVE = 'save';                        // save submit button
    const BTN_FORGOT_PASSWORD = 'forgot-password';  // forgot password link
    const BTN_RESET_PASSWORD = 'reset-password';    // reset password action button
    const BTN_ADMIN_RESET = 'admin-reset';          // reset password for any user by admin
    const BTN_ALREADY_REGISTERED = 'already-reg';   // forgot password link
    const BTN_LOGIN = 'login';                      // login submit button
    const BTN_LOGOUT = 'logout';                    // logout link
    const BTN_NEW_USER = 'new-user';                // new user registration link
    const BTN_REGISTER = 'register';                // registration submit button
    const BTN_SOCIAL_VIEW = 'social-view';          // user social profile view
    const BTN_PROFILE_MANAGE = 'profile-view';        // user profile view button
    const BTN_PROFILE_EDIT = 'profile-edit';        // user profile edit button
    const BTN_USER_CREATE = 'user-create';          // user create button
    const BTN_USER_REFRESH = 'user-refresh';        // user list refresh button

    // the list of model classes
    const MODEL_LOGIN = 'LoginForm';                  // login form model
    const MODEL_USER = 'User';                        // user model
    const MODEL_USER_SEARCH = 'UserSearch';           // user search model
    const MODEL_PROFILE = 'UserProfile';              // user profile model
    const MODEL_SOCIAL_PROFILE = 'SocialProfile';     // social profile model
    const MODEL_PROFILE_SEARCH = 'UserProfileSearch'; // user profile search model
    const MODEL_RECOVERY = 'RecoveryForm';            // user password recovery model

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
     * @var string the icon CSS class prefix to use
     */
    public $iconPrefix = 'glyphicon glyphicon-';

    /**
     * @var array configuration of various buttons used in the application
     */
    public $buttons = [];

    /**
     * @var Closure an anonymous function that will return current timestamp
     * for populating the timestamp fields. Defaults to
     * `function() { return date("Y-m-d H:i:s"); }`
     */
    public $now;
    
    /**
     * @var string the default date time format
     */
    public $datetimeFormat = 'php:Y-m-d H:i:s';
    
    /**
     * @var string the default date format
     */
    public $dateFormat = 'php:Y-m-d';

    /**
     * @var array configuration for superuser data editing accesses. Note that these accesses are
     * available only via administration interface. You can set the following boolean properties:
     * - changeUsername: bool, allow username to be changed for superuser. Defaults to `false`.
     *   If set to `true`, can be changed only by the user who is the superuser.
     * - changeEmail: bool, allow email to be changed for superuser. Defaults to `false`.
     *   If set to `true`, can be changed only by the user who is the superuser.
     * - resetPassword: bool, allow password to be reset for superuser. Defaults to `false`.
     *   If set to `true`, can be reset only by the user who is the superuser.
     *
     * @see `setConfig()` method for the default settings
     */
    public $superuserEditSettings = [];

    /**
     * @var array configuration for admin user data editing accesses. Note that these accesses are
     * available only via administration interface. You can set the following boolean properties:
     * - changeUsername: bool, allow username to be changed for admin. Defaults to `true`.
     *   If set to `true`, can be changed by the superuser OR only by the respective admin user.
     * - changeEmail: bool, allow email to be changed for admin. Defaults to `true`.
     *   If set to `true`, can be changed by the superuser OR only by the respective admin user.
     * - resetPassword: bool, allow password to be reset for admin. Defaults to `true`.
     *   If set to `true`, can be reset by the superuser OR only by the user who is the admin.
     *
     * @see `setConfig()` method for the default settings
     */
    public $adminEditSettings = [];

    /**
     * @var array configuration for normal user data editing. These settings are available
     * only via user account profile interface for normal users. You can set the following 
     * boolean properties:
     * - changeUsername: bool, allow username to be changed for user. Defaults to `true`.
     *   If set to `true`, can be changed by the respective user OR any admin/superuser via
     *   admin user interface. If set to `false` change access will be disabled for all users.
     * - changeEmail: bool, allow email to be changed for user. Defaults to `true`.
     *   If set to `true`, can be changed by the respective user OR any admin/superuser via
     *   admin user interface. Note that `email` change by normal users needs to be revalidated 
     *   by user by following instructions via the system mail sent. If set to `false` change
     *   access will be disabled for all users.
     *
     * @see `setConfig()` method for the default settings
     */
    public $userEditSettings = [];

    /**
     * @var array the settings for the user profile. The following settings can be setup:
     *
     * - enabled: bool, whether the user profile is enabled for the module. Defaults to `true`.
     * - basePath: string, the default file path where uploads will be stored. You can use Yii path
     *   aliases for setting this. Defaults to '@frontend/../uploads'.
     * - baseUrl: string, the absolute baseUrl pointing to the uploads path. Defaults to '/uploads'. 
     *   You must set the full absolute url here to enable avatar URL to be parsed seamlessly across
     *   both frontend and backend apps in yii2-app-advanced.
     * - defaultAvatar: string, the filename for the default avatar located in the above path which will
     *   be displayed when no profile image file is found. Defaults to `avatar.png`.
     * - widget: array|bool, the widget settings for FileInput widget to upload the avatar.
     *   If this is set to `false`, no avatar / image upload will be enabled for the user.
     *
     * @see `setConfig()` method for the default settings
     */
    public $profileSettings = [];

    /**
     * @var array the model settings for the module. The keys will be one of the `Module::MODEL_` constants
     * and the value will be the model class names you wish to set.
     *
     * @see `setConfig()` method for the default settings
     */
    public $modelSettings = [];

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
     * - validateStrengthCurr: array|boolean, the list of forms where password strength will be validated for current password. 
     *   If set to `false` or an empty array, no strength will be validated. The strength will be validated
     *   using `\kartik\password\StrengthValidator`. Defaults to `[Module::UI_INSTALL, Module::UI_RESET]`.
     * - validateStrengthNew: array|boolean, the list of forms where password strength will be validated for new password. 
     *   If set to `false` or an empty array, no strength will be validated. The strength will be validated
     *   using `\kartik\password\StrengthValidator`. Defaults to `[Module::UI_RESET, Module::UI_CHANGEPASS]`.
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
     * - captcha: array|bool, the settings for the captcha action, validator, and widget . If set to `false`, 
     *   no captcha will be displayed. The following settings can be set:
     *   - `action`: array, the captcha action settings.
     *   - `validator`: array, the captcha validator settings.
     *   - `widget`: array, the captcha widget settings.
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
    public $socialSettings = [];


    /**
     * @var array the global widget settings for each form (also available as a widget).
     * Check each widget documentation for details. Few default settings:
     * - `type`: the Bootstrap form orientation - one of `vertical`, `horizontal`, or `inline`.
     * @see `setConfig()` method for the default settings
     */
    public $widgetSettings = [];
    
    /**
     * @var string The prefix for user module URL.
     *
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = 'user';
    
    /**
     * @var array the list of url rules
     */
    public $urlRules = [
        'profile' => 'profile/index',
        'update' => 'profile/update',
        'profile/view/<id:\d+>' => 'profile/view',
        'avatar-delete/<user:>' => 'profile/avatar-delete/<user:>',
        'password' => 'account/password',
        'admin/<id:\d+>' => 'admin/view',
        'admin' => 'admin/index'
    ];

    /**
     * Initialize the module
     */
    public function init()
    {
        $this->_msgCat = 'user';
        parent::init();
        $this->setConfig();
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
            self::ACTION_RECOVERY => 'account/recovery',
            self::ACTION_CAPTCHA => 'account/captcha',
            self::ACTION_SOCIAL_AUTH => 'account/auth',

            // the list of profile actions
            self::ACTION_PROFILE_INDEX => 'profile/index',
            self::ACTION_PROFILE_EDIT => 'profile/update',
            self::ACTION_ACCOUNT_PASSWORD => 'account/password',
            self::ACTION_PROFILE_MANAGE => 'profile/manage',
            
            // the list of avatar actions
            self::ACTION_AVATAR_UPLOAD => 'profile/avatar-upload',
            self::ACTION_AVATAR_DELETE => 'profile/avatar-delete',

            // the list of admin actions
            self::ACTION_ADMIN_LIST => 'admin/index',
            self::ACTION_ADMIN_VIEW => 'admin/view',
            self::ACTION_ADMIN_EDIT => 'admin/update',
            self::ACTION_ADMIN_RESET => 'admin/reset',
        ];
        $this->layoutSettings += [
            // layouts for the various account actions
            self::ACTION_LOGIN => 'install',
            self::ACTION_LOGOUT => 'install',
            self::ACTION_REGISTER => 'install',
            self::ACTION_ACTIVATE => 'install',
            self::ACTION_RESET => 'install',
            self::ACTION_RECOVERY => 'install',
            // the list of social actions
            self::ACTION_SOCIAL_AUTH => 'social/login',
            // the list of profile actions
            self::ACTION_PROFILE_MANAGE => 'profile/view',
            self::ACTION_PROFILE_EDIT => 'profile/update',
            self::ACTION_ACCOUNT_PASSWORD => 'account/password',
            
            // the list of admin actions
            self::ACTION_ADMIN_LIST => 'admin/index',
            self::ACTION_ADMIN_VIEW => 'admin/view',
            self::ACTION_ADMIN_EDIT => 'admin/update',
            self::ACTION_ADMIN_RESET => 'admin/reset',
        ];
        $this->superuserEditSettings += [
            'changeUsername' => false,
            'changeEmail' => false,
            'resetPassword' => false,
        ];
        $this->adminEditSettings += [
            'changeUsername' => true,
            'changeEmail' => true,
            'resetPassword' => true,
        ];
        $this->userEditSettings += [
            'changeUsername' => true,
            'changeEmail' => true
        ];
        $this->loginSettings = array_replace_recursive([
            'loginType' => self::LOGIN_BOTH,
            'rememberMeDuration' => 2592000
        ], $this->loginSettings);
        $this->passwordSettings = array_replace_recursive([
            'validateStrengthCurr' => [self::UI_INSTALL, self::UI_REGISTER],
            'validateStrengthNew' => [Module::UI_RESET, Module::UI_CHANGEPASS],
            'strengthRules' => [
                'min' => 8,
                'upper' => 1,
                'lower' => 1,
                'digit' => 1,
                'special' => 0,
                'hasUser' => true,
                'hasEmail' => true
            ],
            'strengthMeter' => [self::UI_INSTALL, self::UI_REGISTER, Module::UI_RESET, Module::UI_CHANGEPASS],
            'activationKeyExpiry' => 172800,
            'resetKeyExpiry' => 172800,
            'passwordExpiry' => false,
            'wrongAttempts' => false,
            'enableRecovery' => true
        ], $this->passwordSettings);
        $captchaTemplate = <<< HTML
<div class="row" style="margin-bottom:-10px">
    <div class="col-sm-8">
        {input}
    </div>
    <div class="col-sm-4">
        {image}
    </div>
</div>
HTML;
        $this->registrationSettings = array_replace_recursive([
            'enabled' => true,
            'captcha' => [
                'action' => ['class' => 'yii\captcha\CaptchaAction'],
                'widget' => [
                    'captchaAction' => $this->actionSettings[self::ACTION_CAPTCHA],
                    'template' => $captchaTemplate,
                    'imageOptions' => [
                        'title' => Yii::t('user', 'Click image to refresh and get a new code'),
                        'style' => 'height:40px'
                    ],
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => Yii::t('user', 'Enter text as seen in image'),
                    ]
                ],
                'validator' => ['captchaAction' => 'user/' . $this->actionSettings[self::ACTION_CAPTCHA]],
            ],
            'autoActivate' => false,
            'userNameRules' => ['min' => 4, 'max' => 30],
            'userNamePattern' => '/^[A-Za-z0-9_-]+$/u',
            'userNameValidMsg' => Yii::t('user', '{attribute} can contain only letters, numbers, hyphen, and underscore.')
        ], $this->registrationSettings);
        $appName = \Yii::$app->name;
        $supportEmail = isset(\Yii::$app->params['supportEmail']) ? \Yii::$app->params['supportEmail'] : 'nobody@support.com';
        $this->notificationSettings = array_replace_recursive([
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
        ],  $this->notificationSettings);
        $this->socialSettings = array_replace_recursive([
            'enabled' => true,
            'refreshAttributes' => [
                'display_name',
                'email'
            ],
        ], $this->socialSettings);
        $this->profileSettings = array_replace_recursive([
            'enabled' => true,
            'basePath' => '@frontend/../uploads',
            'baseUrl' => '/uploads',
            'defaultAvatar' => 'avatar.png',
            'widget' => [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'elErrorContainer' => '#user-avatar-errors',
                    'allowedFileExtensions' => ['jpg', 'gif', 'png'],
                    'maxFileSize' => 200,
                    'showCaption' => false,
                    'overwriteInitial' => true,
                    'browseLabel' => '',
                    'removeLabel' => '',
                    'removeIcon' => '<i class="glyphicon glyphicon-ban-circle"></i>',
                    'browseIcon' => '<i class="glyphicon glyphicon-folder-open"></i>',
                    'showClose' => false,
                    'showUpload' => false,
                    'removeTitle' => Yii::t('user', 'Cancel or reset changes'),
                    'previewClass' => 'user-avatar',
                    'msgErrorClass' => 'alert alert-block alert-danger',
                    'previewSettings' => [
                        'image' => ['width' => 'auto', 'height' => '180px'],
                    ]
                ]
            ]
        ], $this->profileSettings);
        $this->modelSettings += [
            self::MODEL_LOGIN => 'comyii\user\models\LoginForm',
            self::MODEL_USER => 'comyii\user\models\User',
            self::MODEL_USER_SEARCH => 'comyii\user\models\UserSearch',
            self::MODEL_PROFILE => 'comyii\user\models\UserProfile',
            self::MODEL_SOCIAL_PROFILE => 'comyii\user\models\SocialProfile',
            self::MODEL_PROFILE_SEARCH => 'comyii\user\models\UserProfileSearch',
            self::MODEL_RECOVERY => 'comyii\user\models\RecoveryForm',
        ];
        $this->widgetSettings = array_replace_recursive([
            self::UI_LOGIN => ['type' => 'vertical'],
            self::UI_REGISTER => ['type' => 'horizontal'],
            self::UI_ACTIVATE => ['type' => 'inline'],
            self::UI_RECOVERY => ['type' => 'inline'],
            self::UI_RESET => ['type' => 'vertical'],
            self::UI_PROFILE => ['type' => 'vertical'],
            self::UI_ADMIN => ['type' => 'vertical'],
        ], $this->widgetSettings);
        $this->buttons = array_replace_recursive(static::getDefaultButtonConfig(), $this->buttons);
    }

    /**
     * Superuser already exists in the database?
     * @return bool
     */
    public function hasSuperUser() {
        return count(User::find()->superuser()->all()) > 0;
    }
    
    /**
     * Fetch the icon for a icon identifier
     * @param string $id suffix the icon suffix name
     * @param array $options the icon HTML attributes
     * @param string $prefix the icon css prefix name
     * @return string the parsed icon
     */
    public function icon($id, $options = ['style'=>'margin-right:5px'], $prefix = null)
    {   
        if ($prefix === null) {
            $prefix = $this->iconPrefix;
        }
        Html::addCssClass($options, explode(' ', $prefix . $id));
        return Html::tag('i', '', $options);
    }

    /**
     * Gets the admin configuration ability for a user model record
     * @param User $model the user model
     * @return array|bool
     */
    public function getEditSettingsAdmin($model) {
        if ($model === null) {
            return false;
        }
        $user = Yii::$app->user;
        if ($model->isAccountSuperuser()) {
            if (!$user->isSuperuser || $model->id != $user->id) {
                return false;
            }
            return $this->superuserEditSettings;
        } elseif ($model->isAccountAdmin()) {
            $allowed = $user->isAdmin && $model->id == $user->id || $user->isSuperuser;
            if (!$allowed) {
                return false;
            }
            return $this->adminEditSettings;
        } elseif ($user->isSuperuser || $user->isAdmin) {
            return true;
        }
        return false;
    }

    /**
     * Gets the user configuration ability for a user model record
     * @param User $model the user model
     * @return array|bool
     */
    public function getEditSettingsUser($model) {
        if ($model === null) {
            return false;
        }
        $user = Yii::$app->user;
        if ($model->id != $user->id) {
            return false;
        }
        return $this->userEditSettings;
    }
        
    /**
     * Generates an action button
     * @param string $key the button identification key
     * @param array $params the parameters to pass to the button action
     * @param array $config the button configuration options to override
     */
    public function button($key, $params = [], $config = [])
    {
        $btn = ArrayHelper::getValue($this->buttons, $key, []);
        if (empty($btn)) {
            return '';
        }
        $iconPrefix = $this->iconPrefix;
        $label = $icon = $action = $type = '';
        $options = [];
        $iconOptions = ['style'=>'margin-right:5px'];
        extract($btn);
        if (!empty($icon)) {
            Html::addCssClass($iconOptions, explode(' ', $iconPrefix . $icon));
            $icon = Html::tag('i', '', $iconOptions);
        }
        $label = $icon . $label;
        $options = array_replace_recursive($options, $config);
        if (!empty($options['disabled'])) {
            $action = null;
        }
        if (!empty($action)) {
            $action = ArrayHelper::getValue($this->actionSettings, $action, $action);
            $action = Url::to([$action] + $params);
            return Html::a($label, $action, $options);
        }
        if (!empty($type) && $type === 'submit' || $type === 'reset') {
            $type .= 'Button';
        } else {
            $type = 'button';
        }
        return Html::$type($label, $options);
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
     * Gets the default button configuration
     */
    protected static function getDefaultButtonConfig()
    {
        return [
            self::BTN_HOME => [
                'label' => Yii::t('user', 'Home'),
                'icon' => 'home',
                'action' => '/',
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Back to home'),
                ],
            ],
            self::BTN_BACK => [
                'label' => Yii::t('user', 'Return'),
                'icon' => 'arrow-left',
                'action' => Yii::$app->user->returnUrl,
                'options' => ['class' => 'btn btn-link y2u-link'],
            ],
            self::BTN_RESET_FORM => [
                'type' => 'reset',
                'label' => Yii::t('user', 'Reset Form'),
                'icon' => 'repeat',
                'options' => ['class' => 'btn btn-default'],
            ],
            self::BTN_SUBMIT_FORM => [
                'type' => 'submit',
                'label' => Yii::t('user', 'Submit'),
                'icon' => 'save',
                'options' => ['class' => 'btn btn-primary'],
            ],
            self::BTN_SAVE => [
                'type' => 'submit',
                'label' => Yii::t('user', 'Save'),
                'icon' => 'save',
                'options' => ['class' => 'btn btn-primary'],
            ],
            self::BTN_FORGOT_PASSWORD => [
                'label' => Yii::t('user', 'Forgot Password?'),
                'icon' => 'info-sign',
                'action' => self::ACTION_RECOVERY,
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Recover your lost password')
                ],
            ],
            self::BTN_RESET_PASSWORD => [
                'label' => Yii::t('user', 'Reset Password'),
                'icon' => 'lock',
                'action' => self::ACTION_RECOVERY,
                'options' => ['class' => 'btn btn-default'],
            ],
            self::BTN_ADMIN_RESET => [
                'label' => Yii::t('user', 'Reset Password'),
                'icon' => 'lock',
                'action' => self::ACTION_ADMIN_RESET,
                'options' => ['class' => 'btn btn-sm btn-default'],
            ],
            self::BTN_ALREADY_REGISTERED => [
                'label' => Yii::t('user', 'Already registered?'),
                'icon' => 'hand-up',
                'action' => self::ACTION_LOGIN,
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Click here to login')
                ],
            ],
            self::BTN_LOGIN => [
                'type' => 'submit',
                'label' => Yii::t('user', 'Login'),
                'icon' => 'log-in',
                'options' => ['class' => 'btn btn-primary'],
            ],
            self::BTN_LOGOUT => [
                'label' => Yii::t('user', 'Logout'),
                'action' => self::ACTION_LOGOUT,
                'icon' => 'log-out',
                'options' => ['class' => 'btn btn-link y2u-link'],
            ],
            self::BTN_NEW_USER => [
                'label' => Yii::t('user', 'New user?'),
                'icon' => 'edit',
                'action' => self::ACTION_REGISTER,
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Register for a new user account')
                ],
            ],
            self::BTN_REGISTER => [
                'type' => 'submit',
                'label' => Yii::t('user', 'Register'),
                'icon' => 'edit',
                'options' => ['class' => 'btn btn-primary'],
            ],
            self::BTN_PROFILE_MANAGE => [
                'label' => Yii::t('user', 'Manage User Profile'),
                'icon' => 'eye-open',
                'action' => self::ACTION_PROFILE_MANAGE,
                'options' => [
                    'class' => 'btn btn-sm btn-default',
                    'title' => Yii::t('user', 'View / manage user profile'),
                ],
            ],
            self::BTN_PROFILE_EDIT => [
                'label' => Yii::t('user', 'Edit'),
                'icon' => 'pencil',
                'action' => self::ACTION_PROFILE_EDIT,
                'options' => [
                    'class' => 'btn btn-primary',
                    'title' => Yii::t('user', 'Edit user profile'),
                ],
            ],
            self::BTN_USER_CREATE => [
                'icon' => 'plus',
                'action' => self::ACTION_ADMIN_CREATE,
                'options' => [
                    'class' => 'btn btn-success',
                    'title' => Yii::t('user', 'Add new user'),
                ],
            ],
            self::BTN_USER_REFRESH => [
                'icon' => 'refresh',
                'action' => self::ACTION_ADMIN_LIST,
                'options' => [
                    'class' => 'btn btn-default',
                    'title' => Yii::t('user', 'Refresh user list'),
                ],
            ],
        ];
    }
}