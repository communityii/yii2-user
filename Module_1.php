<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user;

use Yii;
use DateTime;
use yii\base\Model;
use yii\base\InvalidConfigException;
use yii\console\Application as ConsoleApplication;
use yii\helpers\ArrayHelper;
use yii\helpers\FormatConverter;
use yii\helpers\Url;
use yii\swiftmailer\Mailer;
use kartik\helpers\Html;
use kartik\helpers\Enum;
use yii\authclient\Collection;
use comyii\user\models\User;
use comyii\user\widgets\SocialConnectChoice;

/**
 * User module with inbuilt social authentication for Yii framework 2.0.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Module extends \kartik\base\Module
{
    // time shortcuts
    const DAYS_2 = 172800;
    const DAYS_30 = 2592000;
    const DAYS_90 = 7776000;

    // the valid types of login methods
    const LOGIN_USERNAME = 1;
    const LOGIN_EMAIL = 2;
    const LOGIN_BOTH = 3;

    // user statuses
    const STATUS_SUPERUSER = -1;
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_ADMIN = 3;
    const STATUS_EXPIRED = 4;
    const STATUS_LOCKED = 5;

    // the major model scenarios (some of these map to specific user interfaces)
    const SCN_ACCESS = 'access';
    const SCN_INSTALL = 'install';
    const SCN_LOGIN = 'login';
    const SCN_REGISTER = 'register';
    const SCN_ACTIVATE = 'activate';
    const SCN_RESET = 'reset';
    const SCN_CHANGEPASS = 'password';
    const SCN_RECOVERY = 'recovery';
    const SCN_LOCKED = 'locked';
    const SCN_EXPIRY = 'expiry';
    const SCN_PROFILE = 'profile';
    const SCN_ADMIN = 'admin';
    const SCN_ADMIN_CREATE = 'adminCreate';
    const SCN_NEWEMAIL = 'newemail';

    // the list of account actions
    const ACTION_LOGIN = 1;             // login as new user
    const ACTION_LOGOUT = 2;            // logout of account
    const ACTION_REGISTER = 3;          // new account registration
    const ACTION_ACTIVATE = 4;          // account activation
    const ACTION_RECOVERY = 5;          // account password recovery request
    const ACTION_RESET = 6;             // account password reset
    const ACTION_CAPTCHA = 7;           // account captcha for registration
    const ACTION_NEWEMAIL = 8;          // account new email change action
    const ACTION_SOCIAL_AUTH = 15;      // social auth & login

    // the list of profile actions
    const ACTION_PROFILE_INDEX = 50;    // profile index
    const ACTION_PROFILE_VIEW = 51;     // profile view
    const ACTION_PROFILE_UPDATE = 52;   // profile update
    const ACTION_ACCOUNT_PASSWORD = 53; // profile password change
    const ACTION_AVATAR_DELETE = 54;    // profile image delete

    // the list of admin actions (applicable only for admin & superuser)
    const ACTION_ADMIN_INDEX = 100;     // user listing
    const ACTION_ADMIN_VIEW = 101;      // user view
    const ACTION_ADMIN_UPDATE = 102;    // user update
    const ACTION_ADMIN_CREATE = 103;    // user creation

    // the list of views used
    const VIEW_LOGIN = 200;             // login form
    const VIEW_REGISTER = 201;          // new user registration form
    const VIEW_NEWEMAIL = 202;          // new email change confirmation
    const VIEW_PASSWORD = 203;          // password change form
    const VIEW_RECOVERY = 204;          // password recovery form
    const VIEW_RESET = 205;             // password reset form
    const VIEW_ADMIN_INDEX = 206;       // manage users list (for admin & superuser only)
    const VIEW_ADMIN_CREATE = 207;      // create user form (for admin & superuser only)
    const VIEW_ADMIN_UPDATE = 208;      // update user form (for admin & superuser only)
    const VIEW_ADMIN_VIEW = 209;        // update user form (for admin & superuser only)
    const VIEW_PROFILE_INDEX = 210;     // user profile view (for current user only)
    const VIEW_PROFILE_UPDATE = 211;    // user profile update (for current user only)
    const VIEW_PROFILE_VIEW = 212;      // user profile view (for any user viewable by admin & superuser)

    // the list of model classes
    const MODEL_USER = 300;             // user model
    const MODEL_USER_SEARCH = 301;      // user search model
    const MODEL_LOGIN = 302;            // login form model
    const MODEL_PROFILE = 303;          // user profile model
    const MODEL_SOCIAL_PROFILE = 304;   // social profile model
    const MODEL_PROFILE_SEARCH = 305;   // user profile search model
    const MODEL_RECOVERY = 306;         // user password recovery model

    // the list of various action buttons
    const BTN_HOME = 400;               // back to home page
    const BTN_BACK = 401;               // back to previous page
    const BTN_RESET_FORM = 402;         // reset form button
    const BTN_SUBMIT_FORM = 403;        // submit button
    const BTN_FORGOT_PASSWORD = 404;    // forgot password link
    const BTN_ALREADY_REGISTERED = 405; // already registered link
    const BTN_LOGIN = 406;              // login submit button
    const BTN_LOGOUT = 407;             // logout link
    const BTN_NEW_USER = 408;           // new user registration link
    const BTN_REGISTER = 409;           // registration submit button

    // the list of events
    const EVENT_EXCEPTION = 'exception';
    const EVENT_BEFORE_ACTION = 'beforeAction';
    const EVENT_REGISTER_BEGIN = 'beforeRegister';
    const EVENT_REGISTER_COMPLETE = 'registerComplete';
    const EVENT_LOGIN_BEGIN = 'loginBegin';
    const EVENT_LOGIN_COMPLETE = 'loginComplete';
    const EVENT_LOGOUT = 'logout';
    const EVENT_PASSWORD_BEGIN = 'passwordBegin';
    const EVENT_PASSWORD_COMPLETE = 'passwordComplete';
    const EVENT_RECOVERY_BEGIN = 'recoveryBegin';
    const EVENT_RECOVERY_COMPLETE = 'recoveryComplete';
    const EVENT_RESET_BEGIN = 'resetBegin';
    const EVENT_RESET_COMPLETE = 'resetComplete';
    const EVENT_ACTIVATE_BEGIN = 'activateBegin';
    const EVENT_ACTIVATE_COMPLETE = 'activateComplete';
    const EVENT_AUTH_BEGIN = 'authBegin';
    const EVENT_AUTH_COMPLETE = 'authComplete';
    const EVENT_NEWEMAIL_BEGIN = 'newemailBegin';
    const EVENT_NEWEMAIL_COMPLETE = 'newemailComplete';
    const EVENT_PROFILE_INDEX = 'profileIndex';
    const EVENT_PROFILE_VIEW = 'profileView';
    const EVENT_PROFILE_UPDATE = 'profileUpdate';
    const EVENT_PROFILE_UPDATE_BEGIN = 'profileUpdateBegin';
    const EVENT_PROFILE_UPDATE_COMPLETE = 'profileUpdateComplete';
    const EVENT_PROFILE_DELETE_AVATAR_BEGIN = 'profileDeleteAvatarBegin';
    const EVENT_PROFILE_DELETE_AVATAR_COMPLETE = 'profileDeleteAvatarComplete';
    const EVENT_ADMIN_INDEX = 'adminIndex';
    const EVENT_ADMIN_VIEW = 'adminView';
    const EVENT_ADMIN_UPDATE_BEGIN = 'adminUpdateBegin';
    const EVENT_ADMIN_UPDATE_COMPLETE = 'adminUpdateComplete';
    const EVENT_ADMIN_BATCH_UPDATE_BEGIN = 'adminBatchBegin';
    const EVENT_ADMIN_BATCH_UPDATE_COMPLETE = 'adminBatchComplete';
    const EVENT_CREATE_USER_BEGIN = 'createBegin';
    const EVENT_CREATE_USER_COMPLETE = 'createComplete';
    const EVENT_EMAIL_FAILED = 'emailFailed';

    // default layout
    const LAYOUT = 'default';

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
     * @var string the datetime format in which timestamps are stored to database. Use Yii notation
     * to assign formats. Formats prepended with `php:` will use PHP DateTime format, while others
     * will be parsed as ICU notation.
     */
    public $datetimeSaveFormat = 'php:U';

    /**
     * @var string the datetime format in which timestamps are displayed. Use Yii notation to
     * assign formats. Formats prepended with `php:` will use PHP DateTime format, while others
     * will be parsed as ICU notation.
     */
    public $datetimeDispFormat = 'php:M d, Y H:i';

    /**
     * @var \Closure an anonymous function that will return current timestamp
     * for populating the timestamp fields. Defaults to
     * `function() { return time(); }`
     */
    public $now;

    /**
     * @var array the list of user statuses
     */
    public $statuses = [];

    /**
     * @var array the user statuses which are internal to system and not
     * available for direct update by admin or superuser
     */
    public $internalStatuses = [
        self::STATUS_SUPERUSER,
        self::STATUS_PENDING
    ];

    /**
     * @var array the user statuses which are secondary
     */
    public $secondaryStatuses = [
        self::STATUS_LOCKED,
        self::STATUS_EXPIRED,
    ];

    /**
     * @var array the CSS classes for displaying user status as HTML
     */
    public $statusClasses = [
        self::STATUS_SUPERUSER => 'label label-primary',
        self::STATUS_PENDING => 'label label-warning',
        self::STATUS_ACTIVE => 'label label-success',
        self::STATUS_INACTIVE => 'label label-danger',
        self::STATUS_ADMIN => 'label label-info',
        self::STATUS_EXPIRED => 'label label-default',
        self::STATUS_LOCKED => 'label label-danger',
    ];

    /**
     * @var array configuration for superuser data editing accesses. Note that these accesses are
     * available only via administration interface. You can set the following boolean properties:
     * - createUser: bool, whether to allow superuser to create users. Defaults to `true`,
     * - changeUsername: bool, allow username to be changed for superuser. Defaults to `false`.
     *   If set to `true`, can be changed only by the user who is the superuser.
     * - changeEmail: bool, allow email to be changed for superuser. Defaults to `false`.
     *   If set to `true`, can be changed only by the user who is the superuser.
     * - resetPassword: bool, allow password to be reset for superuser. Defaults to `false`.
     *   If set to `true`, can be reset only by the user who is the superuser.
     * - showHiddenInfo: bool, whether to show hidden information of password hash and keys
     *   generated . Defaults to `true`.
     *
     * @see `setConfig()` method for the default settings
     */
    public $superuserEditSettings = [];

    /**
     * @var array configuration for admin user data editing accesses. Note that these accesses are
     * available only via administration interface. You can set the following boolean properties:
     * - createUser: bool, whether to allow admin to create users. Defaults to `true`,
     * - changeUsername: bool, allow username to be changed for admin. Defaults to `true`.
     *   If set to `true`, can be changed by the superuser OR only by the respective admin user.
     * - changeEmail: bool, allow email to be changed for admin. Defaults to `true`.
     *   If set to `true`, can be changed by the superuser OR only by the respective admin user.
     * - resetPassword: bool, allow password to be reset for admin. Defaults to `true`.
     *   If set to `true`, can be reset by the superuser OR only by the user who is the admin.
     * - showHiddenInfo: bool, whether to show hidden information of password hash and keys
     *   generated . Defaults to `false`.
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
     *   aliases for setting this. Defaults to '@webroot/uploads'.
     * - baseUrl: string, the absolute baseUrl pointing to the uploads path. Defaults to '/uploads'.
     *   You must set the full absolute url here to enable avatar URL to be parsed seamlessly across
     *   both frontend and backend apps in yii2-app-advanced.
     * - defaultAvatar: string, the filename for the default avatar located in the above path which will
     *   be displayed when no profile image file is found. Defaults to `avatar.png`.
     * - widgetAvatar: array|bool, the widget settings for FileInput widget to upload the avatar.
     *   If this is set to `false`, no avatar / image upload will be enabled for the user.
     *
     * @see `setConfig()` method for the default settings
     */
    public $profileSettings = [];

    /**
     * @var array the model settings for the module. The keys will be one of the `self::MODEL_` constants
     * and the value will be the model class names you wish to set.
     *
     * @see `setConfig()` method for the default settings
     */
    public $modelSettings = [];

    /**
     * @var array the action settings for the module. The keys will be one of the `self::ACTION_` constants
     * and the value will be the url/route for the specified action.
     * @see `setConfig()` method for the default settings
     */
    public $actionSettings = [];

    /**
     * @var array the view to use for each action in the module. The keys will be one of the `self::VIEW_`
     * constants and the value will be the view file name. The view file name can be combined with Yii
     * path aliases (for example `@frontend/views/user/login`).
     *
     * @see `setConfig()` method for the default settings
     */
    public $viewSettings = [];

    /**
     * @var array the view layout to use for each view in the module. The keys will be one
     * of the `self::VIEW_` constants and the value will be the view layout location.
     */
    public $layoutSettings = [
        self::VIEW_LOGIN => self::LAYOUT,
        self::VIEW_REGISTER => self::LAYOUT,
        self::VIEW_RECOVERY => self::LAYOUT
    ];

    public $userTypes = [];

    /**
     * @var array the login settings for the module. The following options can be set:
     * - loginType: integer, whether users can login with their username, email address, or both.
     *   Defaults to `self::LOGIN_BOTH`.
     * - rememberMeDuration: integer, the duration in seconds for which user will remain logged in on his/her client
     *   using cookies. Defaults to 3600*24*1 seconds (30 days).
     * - loginRedirectUrl: string|array, the default url to redirect after login. Normally the last return
     *   url will be used. This setting will override this behavior and always redirect to this specified url.
     * - logoutRedirectUrl: string|array, the default url to redirect after logout. If not set, it will redirect
     *   to the home page.
     *
     * @see `setConfig()` method for the default settings
     */
    public $loginSettings = [];

    /**
     * @var array the settings for the password in the module. The following options can be set:
     * - validateStrengthCurr: array|boolean, the list of scenarios where password strength will be validated for
     *     current password. If set to `false` or an empty array, no strength will be validated. The strength will be
     *     validated using `\kartik\password\StrengthValidator`. Defaults to `[self::SCN_INSTALL, self::SCN_RESET]`.
     * - validateStrengthNew: array|boolean, the list of scenarios where password strength will be validated for new
     *     password. If set to `false` or an empty array, no strength will be validated. The strength will be validated
     *     using `\kartik\password\StrengthValidator`. Defaults to `[self::SCN_RESET, self::SCN_CHANGEPASS]`.
     * - strengthRules: array, the strength validation rules as required by `\kartik\password\StrengthValidator`
     * - strengthMeter: array|boolean, the list of scenarios where password strength meter will be displayed.
     *   If set to `false` or an empty array, no strength meter will be displayed.  Defaults to
     *   `[self::SCN_REGISTER, self::SCN_RESET]`.
     * - activationKeyExpiry: integer|bool, the time in seconds after which the account activation key/token will
     *     expire. Defaults to 3600*24*2 seconds (2 days). If set to `0` or `false`, the key never expires.
     * - resetKeyExpiry: integer|bool, the time in seconds after which the password reset key/token will expire.
     *   Defaults to 3600*24*2 seconds (2 days). If set to `0` or `false`, the key never expires.
     * - passwordExpiry: integer|bool, the timeout in seconds after which user is required to reset his password
     *   after logging in. Defaults to 90 days. If set to `0` or `false`, the password never expires.
     * - wrongAttempts: integer|bool, the number of consecutive wrong password type attempts, at login, after which
     *   the account is inactivated and needs to be reset. Defaults to `5`. If set to `0` or `false`, the account
     *   is never inactivated after any wrong password attempts.
     * - enableRecovery: bool, whether password recovery is permitted. If set to `true`, users will be given an option
     *   to reset/recover a lost password. Defaults to `true`.
     *
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
     * - randomPasswords: bool, hide the password input in registration form and generate random password.
     *   Defaults to `false`.
     * - randomPasswordMinLength: integer, minimum length of a random generated password. Must be 8 or more.
     *   Note that this value is used for social auth sign ups. Should be at least 8. Defaults to 10.
     * - randomPasswordMaxLength: integer, maximum length of a random generated password. Note that this
     *   value is used for social auth sign ups. Should be greater than minimum password length above.
     *   Defaults to 14.
     * - randomUsernames: bool, hide the username input in registration form and generate random username.
     *   Note that random usernames are generated for social auth sign ups when a nickname or handle is not available.
     *   Defaults to `false`.
     * - randomUsernameGenerator: callable|array, if set to a callable then the callable will be used to generate
     * the random username. If set to array, use as Haikunator config.
     *
     * @see `setConfig()` method for the default settings
     */
    public $registrationSettings = [];

    /**
     * @var array the social authorization settings for the module. The following options should be set:
     * - enabled: bool, whether the social authorization is enabled for the module. Defaults to `true`. If set
     *   to `false`, the remote authentication through social providers will be disabled.
     * - widgetEnabled: bool, whether the display of the widget is enabled in login form, registration form, and
     *   the user profile view. Defaults to `true`. For most cases, you may not need to set this. You can override
     *   the clients displayed by setting the `widgetSocialClass` to use your own AuthChoice widget. But if you need
     *   to totally hide the social connections for some reason, then set this to `false`.
     * - widgetSocial: array, the settings for the yii\authclient\widgets\AuthChoice widget.
     * - widgetSocialClass: string, the classname to use. Will default to `comyii\user\widgets\SocialConnectChoice`,
     *   which extends from `yii\authclient\widgets\AuthChoice` widget.
     * - defaultSuccessUrl: string, the default success url. Defaults to the application home url `Url::home()`.
     * - defaultCancelUrl: string, the default cancel url
     * @see `setConfig()` method for the default settings
     */
    public $socialSettings = [];

    /**
     * @var array the user notification settings for the module. Currently, only email notifications
     * using Yii Swiftmail extension is supported. The following options can be set:
     * - viewPath: string, the path for notification email templates.
     * - activation: array, the settings for the activation notification
     * - recovery: array, the settings for the recovery notification
     * - newemail: array, the settings for the email change notification
     *
     * @see `setConfig()` method for the default settings
     */
    public $notificationSettings = [];

    /**
     * @var string The prefix for user module URL.
     *
     * @see [[yii\web\GroupUrlRule::prefix]]
     */
    public $urlPrefix = 'user';

    /**
     * @var array the list of url rules
     */
    public $urlRules = [
        'profile' => 'profile/index',
        'profile/<id:\d+>' => 'profile/view',
        'update' => 'profile/update',
        'avatar-delete/<user:>' => 'profile/avatar-delete',
        'admin' => 'admin/index',
        'admin/<id:\d+>' => 'admin/view',
        'admin/update/<id:\d+>' => 'admin/update',
        'auth/<authclient:>' => 'account/auth',
        'activate/<key:>' => 'account/activate',
        'reset/<key:>' => 'account/reset',
        'newemail/<key:>' => 'account/newemail',
        'register/<type:>' => 'account/register',
        '<action>' => 'account/<action>',
    ];

    /**
     * @var array the default action settings
     */
    private $_defaultActionSettings = [
        // the list of account actions
        self::ACTION_LOGIN => 'account/login',
        self::ACTION_LOGOUT => 'account/logout',
        self::ACTION_REGISTER => 'account/register',
        self::ACTION_ACTIVATE => 'account/activate',
        self::ACTION_RESET => 'account/reset',
        self::ACTION_RECOVERY => 'account/recovery',
        self::ACTION_CAPTCHA => 'account/captcha',
        self::ACTION_NEWEMAIL => 'account/newemail',
        self::ACTION_SOCIAL_AUTH => 'account/auth',
        // the list of profile actions
        self::ACTION_PROFILE_INDEX => 'profile/index',
        self::ACTION_PROFILE_UPDATE => 'profile/update',
        self::ACTION_ACCOUNT_PASSWORD => 'account/password',
        self::ACTION_PROFILE_VIEW => 'profile/view',
        // the list of avatar actions
        self::ACTION_AVATAR_DELETE => 'profile/avatar-delete',
        // the list of admin actions
        self::ACTION_ADMIN_INDEX => 'admin/index',
        self::ACTION_ADMIN_VIEW => 'admin/view',
        self::ACTION_ADMIN_UPDATE => 'admin/update',
        self::ACTION_ADMIN_CREATE => 'admin/create',
    ];

    /**
     * @var array default behaviors for the controllers. Behaviors defined here will be applied to all the controllers
     * except install and default.
     */
    public $defaultControllerBehaviors = [];

    /**
     * @var array behaviors for the controllers with the key set to the controller id and value as an array of
     *     behaviors.
     */
    public $controllerBehaviors = [];

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
     *
     * @param Model $model
     *
     * @return string
     */
    public static function showErrors($model)
    {
        $errors = [];
        foreach ($model->getAttributes() as $attribute => $setting) {
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
                return time();
            };
        }
        $this->statuses = array_replace_recursive([
            self::STATUS_SUPERUSER => Yii::t('user', 'Superuser'),
            self::STATUS_PENDING => Yii::t('user', 'Pending'),
            self::STATUS_ACTIVE => Yii::t('user', 'Active'),
            self::STATUS_INACTIVE => Yii::t('user', 'Inactive'),
            self::STATUS_ADMIN => Yii::t('user', 'Admin'),
            self::STATUS_LOCKED => Yii::t('user', 'Locked'),
            self::STATUS_EXPIRED => Yii::t('user', 'Expired'),
        ], $this->statuses);
        $this->modelSettings = array_replace_recursive([
            self::MODEL_LOGIN => 'comyii\user\models\LoginForm',
            self::MODEL_USER => 'comyii\user\models\User',
            self::MODEL_USER_SEARCH => 'comyii\user\models\UserSearch',
            self::MODEL_PROFILE => 'comyii\user\models\UserProfile',
            self::MODEL_SOCIAL_PROFILE => 'comyii\user\models\SocialProfile',
            self::MODEL_PROFILE_SEARCH => 'comyii\user\models\UserProfileSearch',
            self::MODEL_RECOVERY => 'comyii\user\models\RecoveryForm',
        ], $this->modelSettings);
        $this->actionSettings = array_replace_recursive($this->_defaultActionSettings, $this->actionSettings);
        $this->profileSettings = array_replace_recursive([
            'enabled' => true,
            'emailChangeKeyExpiry' => static::DAYS_2,
            'basePath' => '@webroot/uploads',
            'baseUrl' => '/uploads',
            'defaultAvatar' => 'avatar.png',
            'widgetAvatar' => [
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
                    'msgErrorClass' => 'alert alert-block alert-danger',
                    'previewSettings' => [
                        'image' => ['width' => 'auto', 'height' => '180px'],
                    ]
                ]
            ]
        ], $this->profileSettings);
        $this->socialSettings = array_replace_recursive([
            'enabled' => true,
            'widgetEnabled' => true,
            'widgetSocial' => [
                'baseAuthUrl' => [$this->actionSettings[Module::ACTION_SOCIAL_AUTH]]
            ],
            'widgetSocialClass' => SocialConnectChoice::classname(),
            'defaultSuccessUrl' => Url::home()
        ], $this->socialSettings);
        $this->superuserEditSettings = array_replace_recursive([
            'createUser' => true,
            'changeUsername' => false,
            'changeEmail' => false,
            'resetPassword' => false,
            'showHiddenInfo' => true
        ], $this->superuserEditSettings);
        $this->adminEditSettings = array_replace_recursive([
            'createUser' => true,
            'changeUsername' => true,
            'changeEmail' => true,
            'resetPassword' => true,
            'showHiddenInfo' => false
        ], $this->adminEditSettings);
        $this->userEditSettings = array_replace_recursive([
            'changeUsername' => true,
            'changeEmail' => true
        ], $this->userEditSettings);
        $this->loginSettings = array_replace_recursive([
            'loginType' => self::LOGIN_BOTH,
            'rememberMeDuration' => self::DAYS_30
        ], $this->loginSettings);
        $this->passwordSettings = array_replace_recursive([
            'validateStrengthCurr' => [self::SCN_INSTALL, self::SCN_REGISTER, self::SCN_ADMIN_CREATE],
            'validateStrengthNew' => [self::SCN_RESET, self::SCN_CHANGEPASS, self::SCN_EXPIRY],
            'strengthRules' => [
                'min' => 8,
                'upper' => 1,
                'lower' => 1,
                'digit' => 1,
                'special' => 0,
                'hasUser' => true,
                'hasEmail' => true
            ],
            'strengthMeter' => [
                self::SCN_INSTALL,
                self::SCN_REGISTER,
                self::SCN_RESET,
                self::SCN_CHANGEPASS,
                self::SCN_ADMIN,
                self::SCN_EXPIRY
            ],
            'activationKeyExpiry' => self::DAYS_2,
            'resetKeyExpiry' => self::DAYS_2,
            'passwordExpiry' => self::DAYS_90,
            'wrongAttempts' => 5,
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
            'userNameValidMsg' => Yii::t(
                'user',
                '{attribute} can contain only letters, numbers, hyphen, and underscore.'
            ),
            'randomPasswords' => false,
            'randomPasswordMinLength' => 10,
            'randomPasswordMaxLength' => 14,
            'randomUsernames' => false,
            'randomUsernameGenerator' => []
        ], $this->registrationSettings);

        $appName = \Yii::$app->name;
        $supportEmail = isset(\Yii::$app->params['supportEmail']) ? \Yii::$app->params['supportEmail'] :
            'nobody@' . $_SERVER['HTTP_HOST'];
        $fromName = Yii::t('user', '{appname}', ['appname' => $appName]);
        $this->viewSettings = array_replace_recursive([
            // views in AccountController
            self::VIEW_LOGIN => 'login',
            self::VIEW_REGISTER => 'register',
            self::VIEW_NEWEMAIL => 'newemail',
            self::VIEW_PASSWORD => 'password',
            self::VIEW_RECOVERY => 'recovery',
            self::VIEW_RESET => 'reset',
            // views in AdminController
            self::VIEW_ADMIN_INDEX => 'index',
            self::VIEW_ADMIN_CREATE => 'create',
            self::VIEW_ADMIN_UPDATE => 'update',
            self::VIEW_ADMIN_VIEW => 'view',
            // views in ProfileController
            self::VIEW_PROFILE_INDEX => 'view',
            self::VIEW_PROFILE_UPDATE => 'update',
            self::VIEW_PROFILE_VIEW => 'view',
        ], $this->viewSettings);
        $this->notificationSettings = array_replace_recursive([
            'viewPath' => '@vendor/communityii/yii2-user/views/mail',
            'activation' => [
                'fromEmail' => $supportEmail,
                'fromName' => $fromName,
                'subject' => Yii::t('user', Yii::t('user', 'Account activation for {appname}', ['appname' => $appName]))
            ],
            'recovery' => [
                'fromEmail' => $supportEmail,
                'fromName' => $fromName,
                'subject' => Yii::t('user', Yii::t('user', 'Account recovery for {appname}', ['appname' => $appName]))
            ],
            'newemail' => [
                'fromEmail' => $supportEmail,
                'fromName' => $fromName,
                'subject' => Yii::t('user', Yii::t('user', 'Email change for {appname}', ['appname' => $appName]))
            ]
        ], $this->notificationSettings);

        if (Yii::$app instanceof ConsoleApplication) {
            return;
        }
        $this->buttons = array_replace_recursive(static::getDefaultButtonConfig(), $this->buttons);
    }

    /**
     * Get edit status list
     *
     * @return string
     */
    public function getValidStatuses()
    {
        $statuses = [];
        $exclude1 = array_flip($this->secondaryStatuses);
        $exclude2 = array_flip($this->internalStatuses);
        foreach ($this->statuses as $status => $name) {
            if (!isset($exclude1[$status]) && !isset($exclude2[$status])) {
                $statuses[$status] = $name;
            }
        }
        return $statuses;
    }

    /**
     * Get edit status list
     *
     * @return string
     */
    public function getEditStatuses()
    {
        $statuses = [];
        $exclude = array_flip($this->secondaryStatuses);
        foreach ($this->statuses as $status => $name) {
            if (!isset($exclude[$status])) {
                $statuses[$status] = $name;
            }
        }
        return $statuses;
    }

    /**
     * Get disabled statuses
     *
     * @return array
     */
    public function getDisabledStatuses()
    {
        $options = [];
        foreach ($this->internalStatuses as $status) {
            $options[$status] = ['disabled' => true];
        }
        return $options;
    }

    /**
     * Get primary status list
     *
     * @return string
     */
    public function getPrimaryStatuses()
    {
        $statuses = [];
        $exclude = array_flip($this->secondaryStatuses);
        foreach ($this->statuses as $status => $name) {
            if (!isset($exclude[$status])) {
                $statuses[$status] = $name;
            }
        }
        return $statuses;
    }

    /**
     * Get secondary status list
     *
     * @return string
     */
    public function getSecondaryStatuses()
    {
        $statuses = [];
        foreach ($this->secondaryStatuses as $status) {
            $statuses[$status] = $this->statuses[$status];
        }
        return $statuses;
    }

    /**
     * Superuser already exists in the database?
     *
     * @return bool
     */
    public function hasSuperUser()
    {
        /**
         * @var User $class
         */
        $class = $this->modelSettings[Module::MODEL_USER];
        return $class::find()->superuser()->exists();
    }

    /**
     * Fetch the icon for a icon identifier
     *
     * @param string $id suffix the icon suffix name
     * @param array  $options the icon HTML attributes
     * @param string $prefix the icon css prefix name
     *
     * @return string the parsed icon
     */
    public function icon($id, $options = ['style' => 'margin-right:5px'], $prefix = null)
    {
        if ($prefix === null) {
            $prefix = $this->iconPrefix;
        }
        Html::addCssClass($options, explode(' ', $prefix . $id));
        return Html::tag('i', '', $options);
    }

    /**
     * Gets the admin configuration ability for a user model record
     *
     * @param User $model the user model
     *
     * @return array|bool
     */
    public function getEditSettingsAdmin($model)
    {
        if ($model === null) {
            return false;
        }
        $user = Yii::$app->user;
        if ($model->isSuperuser()) {
            if (!$user->isSuperuser || $model->id != $user->id) {
                return false;
            }
            return $this->superuserEditSettings;
        } elseif ($model->isAdmin()) {
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
     * Checks superuser and admin settings and returns if valid
     *
     * @param array $settings
     * @param mixed $key
     *
     * @return bool|mixed
     */
    public function checkSettings($settings, $key)
    {
        if ($key == 'createUser') {
            $settings = Yii::$app->user->isSuperuser ? $this->superuserEditSettings : $this->adminEditSettings;
            return ArrayHelper::getValue($settings, $key, false);
        }
        return $settings === true || (is_array($settings) && ArrayHelper::getValue($settings, $key, false));
    }

    /**
     * Gets the user configuration ability for a user model record
     *
     * @param User $model the user model
     *
     * @return array|bool
     */
    public function getEditSettingsUser($model)
    {
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
     *
     * @param string $key the button identification key
     * @param array  $params the parameters to pass to the button action.
     * @param array  $config the button configuration options to override. You can additionally set the `label` or
     * `icon` here.
     *
     * @return string
     */
    public function button($key, $params = [], $config = [])
    {
        $btn = ArrayHelper::getValue($this->buttons, $key, []);
        if (empty($btn)) {
            return '';
        }
        $iconPrefix = $this->iconPrefix;
        $labelNew = ArrayHelper::remove($config, 'label', '');
        $iconNew = ArrayHelper::remove($config, 'icon', '');
        $label = $icon = $action = $type = '';
        $options = [];
        $iconOptions = ['style' => 'margin-right:5px'];
        extract($btn);
        if (!empty($iconNew)) {
            $icon = $iconNew;
        }
        if (!empty($icon)) {
            Html::addCssClass($iconOptions, explode(' ', $iconPrefix . $icon));
            $icon = Html::tag('i', '', $iconOptions);
        }
        if (!empty($labelNew)) {
            $label = $labelNew;
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
     *
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
            self::BTN_FORGOT_PASSWORD => [
                'label' => Yii::t('user', 'Forgot Password?'),
                'icon' => 'info-sign',
                'action' => self::ACTION_RECOVERY,
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Recover your lost password')
                ],
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
        ];
    }

    /**
     * Return the default action settings
     *
     * @return array
     */
    public function getDefaultActionSettings()
    {
        return $this->_defaultActionSettings;
    }

    /**
     * Merge class configurations
     *
     * @param array $config
     * @param array $defaults
     *
     * @return array the merged array
     */
    public static function mergeDefault($config, $defaults)
    {
        foreach ($defaults as $key => $default) {
            if (!isset($config[$key])) {
                $config[$key] = $default;
            } elseif (!isset($config[$key]['class'])) {
                $config[$key] = array_replace_recursive($config[$key], $default);
            }
        }
        return $config;
    }

    /**
     * Get module setting
     *
     * @param string $settings the parameter to get
     * @param string $param the parameter to get
     * @param string $userType the user type. Defaults to current user type.
     * @param string $default the default value
     *
     * @return mixed
     */
    private function getSetting($settings, $param, $userType = null, $default = null)
    {
        if (!$userType) {
            $userType = Yii::$app->user->getType();
        }
        if ($userType && isset($this->{$settings}[$userType][$param])) {
            return $this->{$settings}[$userType][$param];
        }
        return ArrayHelper::getValue($this->{$settings}, $param, $default);
    }

    /**
     * Get the layout file for the current view and user type.
     *
     * @param string $view the view to get
     * @param string $default the default value
     * @param string $userType the user type. Defaults to current user type.
     *
     * @return string the layout file
     */
    public function getLayout($view, $default = null, $userType = null)
    {
        return $this->getSetting('layoutSettings', $view, $userType, $default);
    }

    /**
     * Get model setting
     *
     * @param string $setting the parameter to get
     * @param string $userType the user type. Defaults to current user type.
     * @param string $default the default value
     *
     * @return mixed
     */
    public function getModelSetting($setting, $userType = null, $default = null)
    {
        return $this->getSetting('modelSettings', $setting, $userType, $default);
    }

    /**
     * Get registration setting
     *
     * @param string $setting the parameter to get
     * @param string $userType the user type. Defaults to current user type.
     * @param string $default the default value
     *
     * @return mixed
     */
    public function getRegistrationSetting($setting, $userType = null, $default = null)
    {
        return $this->getSetting('registrationSettings', $setting, $userType, $default);
    }

    /**
     * Get social setting
     *
     * @param string $setting the parameter to get
     * @param string $userType the user type. Defaults to current user type.
     * @param string $default the default value
     *
     * @return mixed
     */
    public function getSocialSetting($setting, $userType = null, $default = null)
    {
        return $this->getSetting('socialSettings', $setting, $userType, $default);
    }

    /**
     * Get login setting
     *
     * @param string $setting the parameter to get
     * @param string $default the default value
     * @param string $userType the user type. Defaults to current user type.
     *
     * @return mixed
     */
    public function getLoginSetting($setting, $default = null, $userType = null)
    {
        return $this->getSetting('loginSettings', $setting, $userType, $default);
    }

    /**
     * Get password setting
     *
     * @param string $setting the parameter to get
     * @param string $userType the user type. Defaults to current user type.
     * @param string $default the default value
     *
     * @return mixed
     */
    public function getPasswordSetting($setting, $userType = null, $default = null)
    {
        return $this->getSetting('passwordSettings', $setting, $userType, $default);
    }

    /**
     * Get profile setting
     *
     * @param string $setting the parameter to get
     * @param string $default the default value
     * @param string $userType the user type. Defaults to current user type.
     *
     * @return mixed
     */
    public function getProfileSetting($setting, $default = null, $userType = null)
    {
        return $this->getSetting('profileSettings', $setting, $userType, $default);
    }

    /**
     * Get notification setting
     *
     * @param string $setting the parameter to get
     * @param string $default the default value
     * @param string $userType the user type. Defaults to current user type.
     *
     * @return mixed
     */
    public function getNotificationSetting($setting, $default = null, $userType = null)
    {
        return $this->getSetting('notificationSettings', $setting, $userType, $default);
    }

    /**
     * Get action setting
     *
     * @param string $action the parameter to get
     * @param string $default the default value
     * @param string $userType the user type. Defaults to current user type.
     *
     * @return mixed
     */
    public function getActionSetting($action, $default = null, $userType = null)
    {
        return $this->getSetting('actionSettings', $action, $userType, $default);
    }

    /**
     * Get user edit setting
     *
     * @param string $setting the parameter to get
     * @param string $default the default value
     * @param string $userType the user type. Defaults to current user type.
     *
     * @return mixed
     */
    public function getUserEditSetting($setting, $default = null, $userType = null)
    {
        return $this->getSetting('userEditSettings', $setting, $userType, $default);
    }

    /**
     * Get the controller behaviors configuration
     *
     * @param string $id the controller identifier
     * @param string $userType the user type. Defaults to current user type.
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function getControllerBehaviors($id, $userType = null)
    {
        $controllerBehaviors = $this->getSetting('controllerBehaviors', $id, $userType, null);

        if ($controllerBehaviors === null) {
            return $this->defaultControllerBehaviors;
        }
        if (!is_array($controllerBehaviors)) {
            throw new InvalidConfigException("Controller behaviors must be an array");
        }
        return self::mergeDefault($controllerBehaviors, $this->defaultControllerBehaviors);
    }

    /**
     * Get url rules
     *
     * @param string $setting the parameter to get
     * @param string $userType the user type. Defaults to current user type.
     * @param string $default the default value
     *
     * @return mixed
     */
    public function getUrlRules($setting, $userType = null, $default = null)
    {
        return $this->getSetting('urlRules', $setting, $userType, $default);
    }

    /**
     * Helper to convert expiry time left from now
     *
     * @param string $type the expiry time type
     * @param int    $seconds the time left in seconds
     *
     * @return string
     */
    public static function timeLeft($type, $seconds)
    {
        if ($seconds > 0) {
            return Yii::t('user', 'The {type} link will expire in {time} from now.', [
                'type' => $type,
                'time' => Enum::timeInterval($seconds, '')
            ]);
        }
        return '';
    }

    public function isUserType($userType)
    {
        return isset($this->userTypes[$userType]);
    }

    /**
     * Whether the module has social authorization enabled
     */
    public function hasSocialAuth()
    {
        if (!$this->socialSettings['enabled']) {
            return false;
        }
        try {
            /** @noinspection PhpUndefinedFieldInspection */
            $valid = Yii::$app->authClientCollection instanceof Collection;
        } catch (\Exception $e) {
            $valid = false;
        }
        if (!$valid) {
            throw new InvalidConfigException("You must setup the `authClientCollection` component and its `clients` in your app configuration file.");
        }
        return true;
    }

    /**
     * Get timestamp as integer or date time object or as a specific format
     *
     * @param string      $source the timestamp string
     * @param bool|string $format
     * - if set to `true` will return as PHP DateTime object
     * - if set to `false` will return as integer
     * - if string - will be treated as the format
     *
     * @return integer
     */
    public function timestamp($source, $format = true)
    {
        $fmtSource = !empty($this->datetimeSaveFormat) ? $this->datetimeSaveFormat : 'php:U';
        $fmtSource = strncmp($fmtSource, 'php:', 4) === 0 ? substr($fmtSource, 4) :
            FormatConverter::convertDateIcuToPhp($fmtSource, 'datetime');
        try {
            $timestamp = DateTime::createFromFormat($fmtSource, $source);
        } catch (\Exception $e) {
            return null;
        }
        return $format === true ? $timestamp :
            ($format === false ? date_format($timestamp, 'U') : date_format($timestamp, $format));
    }

    /**
     * Displays a timestamp from an attribute in a model if valid - else null if empty or invalid
     *
     * @param Model  $model the model
     * @param string $attr the timestamp attribute
     *
     * @return int|string
     */
    public static function displayAttrTime($model, $attr)
    {
        return isset($model->$attr) ? static::displayTime($model->$attr) : null;
    }

    /**
     * Displays a timestamp if valid - else null if empty or invalid
     *
     * @param int|string the timestamp
     *
     * @return int|string
     */
    public static function displayTime($timestamp)
    {
        return isset($timestamp) && strtotime($timestamp) ? $timestamp : null;
    }

    /**
     * Gets the default SocialConnect widget based on `socialSettings`
     *
     * @return string
     */
    public function getSocialWidget()
    {
        $config = $this->socialSettings;
        $class = $config['widgetSocialClass'];
        $settings = $config['widgetSocial'];
        /** @var SocialConnectChoice $class */
        return $class::widget($settings);
    }

    /**
     * Send an email notification
     *
     * @param string $type the type of email notification (view name)
     * @param User   $model the user model
     * @param array  $params additional parameters to be parsed and replaced in the mail template
     * @param string $to the email address to send email to. If not passed,
     * will default to `$model->email`.
     *
     * @return bool
     */
    public function sendEmail($type, $model, $params = [], $to = null)
    {
        if (!empty($this->notificationSettings[$type])) {
            $settings = $this->notificationSettings[$type];
            /**
             * @var Mailer $mailer
             */
            $mailer = Yii::$app->mailer;
            $mailer->viewPath = $this->notificationSettings['viewPath'];
            if (empty($to)) {
                $to = $model->email;
            }
            return $mailer
                ->compose($type, ['user' => $model] + $params)
                ->setFrom([$settings['fromEmail'] => $settings['fromName']])
                ->setTo($to)
                ->setSubject($settings['subject'])
                ->send();
        }
        return false;
    }
}
