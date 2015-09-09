<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\IdentityInterface;
use kartik\password\StrengthValidator;
use comyii\user\Module;

/**
 * This is the model class for table {{%user}}.
 *
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $email_new
 * @property string $password_hash
 * @property string $auth_key
 * @property string $activation_key
 * @property string $reset_key
 * @property string $email_change_key
 * @property integer $status
 * @property string $created_on
 * @property string $updated_on
 * @property string $last_login_ip
 * @property string $last_login_on
 * @property string $password_reset_on
 * @property string $password_fail_attempts
 * @property string $password write-only password
 * @property string $password_new write-only password
 * @property string $password_confirm write-only password
 * @property string $captcha the captcha for registration
 *
 * @property RemoteIdentity[] $remoteIdentities
 * @property UserProfile $userProfile
 *
 * @method \comyii\user\models\UserQuery|static|null find($q = null) static
 * @method \comyii\user\models\UserQuery findBySql($sql, $params = []) static
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class User extends BaseModel implements IdentityInterface
{
    const STATUS_SUPERUSER = -1;
    const STATUS_PENDING  = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_ADMIN = 3;
    const STATUS_LOCKED = 4;
    const STATUS_EXPIRED = 5;

    /**
     * @var string the write only password
     */
    public $password;

    /**
     * @var string the new password (required for password change)
     */
    public $password_new;

    /**
     * @var string the new password confirmation
     * (required during reset and password change)
     */
    public $password_confirm;

    /**
     * @var string the captcha if applicable
     */
    public $captcha;

    /**
     * @var array the list of statuses
     */
    private $_statuses = [];

    /**
     * @var array the list of status CSS classes
     */
    private $_statusClasses = [];

    /**
     * Table name for the User model
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Creates query for this model
     *
     * @return UserQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_statuses = [
            self::STATUS_SUPERUSER => Yii::t('user', 'Superuser'),
            self::STATUS_PENDING => Yii::t('user', 'Pending'),
            self::STATUS_ACTIVE => Yii::t('user', 'Active'),
            self::STATUS_INACTIVE => Yii::t('user', 'Inactive'),
            self::STATUS_ADMIN => Yii::t('user', 'Admin'),
        ];
        $this->_statusClasses = [
            self::STATUS_SUPERUSER => 'label label-primary',
            self::STATUS_PENDING => 'label label-warning',
            self::STATUS_ACTIVE => 'label label-success',
            self::STATUS_INACTIVE => 'label label-danger',
            self::STATUS_ADMIN => 'label label-info',
        ];
    }

    /**
     * User model validation rules
     *
     * @return array
     */
    public function rules()
    {
        $m = $this->_module;
        $config = $m->registrationSettings;
        $rules = [
            ['username', 'match', 'pattern' => $config['userNamePattern'], 'message' => $config['userNameValidMsg']],
            ['username', 'string'] + $config['userNameRules'],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => self::classname(), 'message' => Yii::t('user', 'This username has already been taken')],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::classname(), 'message' => Yii::t('user', 'This email address is already registered')],

            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => array_keys($this->_statuses)],

            ['password', 'required', 'on' => [Module::SCN_REGISTER, Module::SCN_NEWEMAIL]],
            [['password_new', 'password_confirm'], 'required', 'on' => [Module::SCN_RESET, Module::SCN_CHANGEPASS]],
            ['password_confirm', 'compare', 'compareAttribute' => 'password_new', 'on' => [Module::SCN_RESET, Module::SCN_CHANGEPASS]], 
            
            ['password', 'required', 'on' => [Module::SCN_CHANGEPASS, Module::SCN_NEWEMAIL]],
            ['password', 'isValidPassword', 'on' => [Module::SCN_CHANGEPASS, Module::SCN_NEWEMAIL]],
            [   
                'password_new', 
                'compare', 
                'compareAttribute' => 'password', 
                'operator'=>'!=', 
                'on' => Module::SCN_CHANGEPASS, 
                'message' => Yii::t('user', 'Your new password cannot be same as your existing password')
            ], 
        ];
        if ($m->registrationSettings['captcha'] !== false) {
            $config = ArrayHelper::getValue($m->registrationSettings['captcha'], 'validator', []);
            $rules[] = ['captcha', 'captcha'] + $config + ['on' => Module::SCN_REGISTER];
        }
        $strengthRules = $m->passwordSettings['strengthRules'];
        if (($scenarios = $m->passwordSettings['validateStrengthCurr'])) {
            $rules[] = ['password', StrengthValidator::className()] + $strengthRules + ['on' => $scenarios];
        }
        if (($scenarios = $m->passwordSettings['validateStrengthNew'])) {
            $rules[] = ['password_new', StrengthValidator::className()] + $strengthRules + ['on' => $scenarios];
        }
        return $rules;

    }

    /**
     * Validates the current password before change
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function isValidPassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        if (!$this->validatePassword($this->$attribute)) {
            $this->addError($attribute, Yii::t('user', 'Invalid password entered'));
        }

    }

    /**
     * User model scenarios
     *
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[Module::SCN_REGISTER] = ['username', 'password', 'email', 'captcha', 'status'];
        $scenarios[Module::SCN_RESET] = ['password_new', 'password_confirm'];
        $scenarios[Module::SCN_CHANGEPASS] = ['password', 'password_new', 'password_confirm'];
        $scenarios[Module::SCN_INSTALL] = ['username', 'password', 'email', 'status'];
        $scenarios[Module::SCN_NEWEMAIL] = ['password'];
        $m = $this->_module;
        $settings = [];
        // for admin user and superuser
        $editSettings = $m->getEditSettingsAdmin($this);
        if (is_array($editSettings)) {
            if ($editSettings['changeUsername']) {
                $settings[] = 'username';
            }
            if ($editSettings['changeEmail']) {
                $settings[] = 'email';
            }
        } elseif ($editSettings === true) {
            $settings = ['username', 'email'];
        }
        if (!empty($settings)) {
            $scenarios[Module::SCN_ADMIN] = ['status'] + $settings;
        }
        // for normal user
        $settings = ['email_new', 'email_change_key'];
        $editSettings = $m->getEditSettingsUser($this);
        if (is_array($editSettings)) {
            if ($editSettings['changeUsername']) {
                $settings[] = 'username';
            }
            if ($editSettings['changeEmail']) {
                $settings[] = 'email';
            }
        }
        if (!empty($settings)) {
            $scenarios[Module::SCN_PROFILE] = $settings;
        }
        return $scenarios;
    }

    /**
     * Attribute labels for the User model
     *
     * @return array
     */
    public function attributeLabels()
    {
        $m = $this->_module;
        $status = Yii::t('user', 'Status');
        return [
            'id' => Yii::t('user', 'ID'),
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'email_new' => Yii::t('user', 'New Email Requested'),
            'password_hash' => Yii::t('user', 'Password Hash'),
            'auth_key' => Yii::t('user', 'Authorization Key'),
            'activation_key' => Yii::t('user', 'Activation Key'),
            'reset_key' => Yii::t('user', 'Reset Key'),
            'status' => $status,
            'statusText' => $status,
            'statusHtml' => $status,
            'fullName' => Yii::t('user', 'Full Name'),
            'created_on' => Yii::t('user', 'Created On'),
            'updated_on' => $this->scenario == Module::SCN_CHANGEPASS ? Yii::t('user', 'Last Updated On') : Yii::t('user', 'Updated On'),
            'last_login_ip' => Yii::t('user', 'Last Login From'),
            'last_login_on' => Yii::t('user', 'Last Login On'),
            'password_reset_on' => Yii::t('user', 'Password Reset On'),
            'password_fail_attempts' => Yii::t('user', 'Password Fail Attempts'), 
            'password' => $this->scenario == Module::SCN_CHANGEPASS ? Yii::t('user', 'Current Password') : Yii::t('user', 'Password'),
            'password_new' => Yii::t('user', 'New Password'),
            'password_confirm' => Yii::t('user', 'Confirm Password')
        ];
    }

    /**
     * Get user details link
     *
     * @return string
     */
    public function getUserLink($showId = false)
    {
        $label = $showId ? $this->id : $this->username;
        $m = $this->_module;
        $url = $m->actionSettings[Module::ACTION_ADMIN_MANAGE];
        return Html::a($label, [$url, 'id' => $this->id], [
            'data-pjax'=>'0', 
            'title' => Yii::t('user', 'View user details')
        ]);
    }

    /**
     * Remote identities relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocialProfile()
    {
        return $this->hasMany(SocialProfile::className(), ['user_id' => 'id']);
    }

    /**
     * User profile relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(UserProfile::className(), ['id' => 'id']);
    }

    /**
     * Is password expired
     *
     * @return bool
     */
    public function isPasswordExpired()
    {
        if ($this->_module->passwordSettings['passwordExpiry'] > 0) {
            $expiry = time() - strtotime($this->password_reset_on);
            return ($expiry >= $this->_module->passwordSettings['passwordExpiry']);
        }
        return false;
    }

    /**
     * Is account active
     *
     * @return bool
     */
    public function isAccountSuperuser()
    {
        return $this->status === self::STATUS_SUPERUSER;
    }

    /**
     * Is account active
     *
     * @return bool
     */
    public function isAccountBanned()
    {
        return $this->status === self::STATUS_BANNED;
    }

    /**
     * Is account active
     *
     * @return bool
     */
    public function isAccountAdmin()
    {
        return $this->status === self::STATUS_SUPERUSER || $this->status === self::STATUS_ADMIN;
    }

    /**
     * Is account active
     *
     * @return bool
     */
    public function isAccountActive()
    {
        return $this->status === self::STATUS_ACTIVE 
            || $this->status === self::STATUS_SUPERUSER
            || $this->status === self::STATUS_ADMIN;
    }

    /**
     * Is account locked due to failed attempts
     *
     * @return bool
     */
    public function isAccountLocked()
    {
        $attempts = $this->_module->passwordSettings['wrongAttempts'];
        return ($attempts > 0 && $this->password_fail_attempts > $attempts);
    }

    /**
     * Validate failed login attempt
     *
     * @param bool $outcome the password validation outcome
     */
    public function checkFailedLogin($outcome)
    {
        if ($this->_module->passwordSettings['wrongAttempts'] > 0) {
            if ($outcome) {
                $this->password_fail_attempts = 0;
            } else {
                $this->password_fail_attempts += 1;
            }
            $this->save();
        }
    }

    /**
     * Sets the model status
     */
    public function saveStatus($status)
    {
        if ($this->status != $status) {
            $this->status = $status;
            $this->save();
        }
    }

    /**
     * Get user identity
     */
    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->active()->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
    

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->active()->one();
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::find()->where(['email' => $email])->active()->one();
    }

    /**
     * Finds user by username or email
     *
     * @param string $userStr
     * @return static|null
     */
    public static function findByUserOrEmail($input)
    {
        return static::find()->where(['username' => $input])->orWhere(['email' => $input])->active()->one();
    }

    /**
     * Finds user by either auth_key, reset_key, activation_key, 
     * or email_change_key
     *
     * @param string $attribute the key attribute name
     * @param string $value key attribute value
     * @param integer $expire key expiry
     *
     * @return static|null
     */
    public static function findByKey($attribute, $value, $expire = 0)
    {
        if (!static::isKeyValid($value, $expire)) {
            return null;
        }
        return static::find()->where([$attribute => $value])->active()->one();
    }

    /**
     * Check if a key value is valid
     *
     * @param string $value key value
     * @param integer $expire the expiry time in seconds
     *
     * @return bool
     */
    public static function isKeyValid($value, $expire)
    {
        if ($expire === 0) {
            return true;
        }
        if (empty($value)) {
            return false;
        }
        $parts = explode('_', $value);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * Generates a hash key
     *
     * @param $expire integer the expiry time in seconds
     *
     * @return bool
     */
    public static function generateKey($expire = 0)
    {
        $key = Yii::$app->security->generateRandomString();
        return (!empty($expire) && $expire > 0) ? $key . '_' . time() : $key;
    }

    /**
     * Sets the last login ip and time
     */
    public function setLastLogin()
    {
        $this->password_fail_attempts = 0;
        $this->last_login_ip = Yii::$app->getRequest()->getUserIP();
        $this->last_login_on = call_user_func($this->_module->now);
        $this->save();
    }

    /**
     * Get user identifier
     *
     * @return string
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Get authorization key
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validate authorization key
     *
     * @param string $authKey the authorization key
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = self::generateKey($this->authKeyExpiry);
    }

    /**
     * Generates new password reset key
     */
    public function generateResetKey()
    {
        $this->reset_key = self::generateKey($this->resetKeyExpiry);
    }

    /**
     * Generates new activation key
     */
    public function generateActivationKey()
    {
        $this->activation_key = self::generateKey($this->activationKeyExpiry);
    }

    /**
     * Generates new email change key
     */
    public function generateEmailChangeKey()
    {
        $this->email_change_key = self::generateKey($this->emailChangeKeyExpiry);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Get auth key expiry
     */
    public function getAuthKeyExpiry()
    {
        return ArrayHelper::getValue($this->_module->loginSettings, 'rememberMeDuration', 0);
    }

    /**
     * Get reset key expiry
     */
    public function getResetKeyExpiry()
    {
        return ArrayHelper::getValue($this->_module->passwordSettings, 'resetKeyExpiry', 0);
    }

    /**
     * Get activation key expiry
     */
    public function getActivationKeyExpiry()
    {
        return ArrayHelper::getValue($this->_module->passwordSettings, 'activationKeyExpiry', 0);
    }

    /**
     * Get email key expiry
     */
    public function getEmailChangeKeyExpiry()
    {
        return ArrayHelper::getValue($this->_module->profileSettings, 'emailChangeKeyExpiry', 0);
    }

    /**
     * Get status list
     *
     * @return string
     */
    public function getAllStatusList()
    {
        return $this->_statuses;
    }

    /**
     * Get status list
     *
     * @return string
     */
    public function getStatusList()
    {
        $statuses = $this->_statuses;
        unset($statuses[self::STATUS_SUPERUSER]);
        return $statuses;
    }

    /**
     * User friendly status name
     *
     * @return string
     */
    public function getStatusText()
    {
        return $this->_statuses[$this->status];
    }

    /**
     * Formatted status name
     *
     * @return string
     */
    public function getStatusHtml()
    {
        return '<span class="' . $this->_statusClasses[$this->status] . '">' . $this->statusText . '</span>';
    }

    /**
     * Full Person Name
     *
     * @return string
     */
    public function getFullName()
    {
        $profile = $this->profile;
        if ($profile === null) {
            return null;
        }
        return trim($profile->first_name . ' ' . $profile->last_name);
    }
    
    /**
     * Validates and prepares email for a change
     * @param string $emailOld the old email
     * @return string
     */    
    public function validateEmailChange($emailOld)
    {
        if ($emailOld != $this->email) {
            $this->email_new = $this->email;
            $this->email = $emailOld;
            if (!static::isKeyValid($this->email_change_key, $this->emailChangeKeyExpiry)) {
                $this->generateEmailChangeKey();
            }
            return true;
        }
        return false;
    }

    /**
     * Sends an email with a link, for account activation or new email change
     *
     * @param string $type the type of email - 'activation' or 'recovery'  or 'newemail'
     * @param string $timeLeft the action link expiry information
     * @return bool whether the email was sent
     */
    public function sendEmail($type, $timeLeft)
    {
        $m = $this->_module;
        if ($type == 'activation') {
            return $m->sendEmail($type, $this, ['timeLeft' => $timeLeft]);
        } elseif ($type == 'recovery') {
            return $m->sendEmail('recovery', $this, ['timeLeft' => $timeLeft]);
        } elseif ($type == 'newemail') {
            if ($m->sendEmail('newemail', $this, ['timeLeft' => $timeLeft], $this->email_new)) {
                return true;
            }
            $this->email_new = null;
            $this->email_change_key = null;
            return false;
        } 
        return false;
    }
}
