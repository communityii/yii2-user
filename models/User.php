<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use kartik\password\StrengthValidator;
use communityii\user\Module;
use communityii\user\models\UserBanLog;
//use communityii\user\components\IdentityInterface;

/**
 * This is the model class for table {{%user}}.
 *
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $auth_key
 * @property string $activation_key
 * @property string $reset_key
 * @property integer $status
 * @property string $created_on
 * @property string $updated_on
 * @property string $last_login_ip
 * @property string $last_login_on
 * @property string $password_reset_on
 * @property string $password_fail_attempts
 * @property string $password_raw write-only password
 * @property string $password_new write-only password
 * @property string $password_confirm write-only password
 *
 * @property MailQueues $mailQueues
 * @property RemoteIdentity[] $remoteIdentities
 * @property UserBanLogs $userBanLogs
 * @property UserProfile $userProfile
 *
 * @method \communityii\user\models\UserQuery|static|null find($q = null) static
 * @method \communityii\user\models\UserQuery findBySql($sql, $params = []) static
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class User extends BaseModel implements IdentityInterface
{
    const STATUS_SUPERUSER = -1;
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;
    const STATUS_INACTIVE = 3;

    /**
     * @var the write only password
     */
    public $password_raw;

    /**
     * @var the write only new password (required for password change)
     */
    public $password_new;

    /**
     * @var the write only new password confirmation (required for password change)
     */
    public $password_confirm;

    /**
     * @var array the list of statuses
     */
    private $_statuses = [];

    /**
     * @var array the list of status CSS classes
     */
    private $_statusClasses = [];

    /**
     * @var integer, the auth key ("remember me") expiry time in seconds
     */
    private $_authKeyExpiry;

    /**
     * @var integer, the reset key expiry time in seconds
     */
    private $_resetKeyExpiry;

    /**
     * @var integer, the activation key expiry time in seconds
     */
    private $_activationKeyExpiry;

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
     * Creates a new user
     *
     * @param array $attributes the attributes given by field => value
     * @return static|null the newly created model, or null on failure
     */
    public static function create($attributes, $scenario = null)
    {
        /** @var User $user */
        $user = ($scenario == null) ? new static() : new static(['scenario' => $scenario]);
        $user->setAttributes($attributes);
        $user->setPassword($attributes['password_raw']);
        $user->generateAuthKey();
        $status = ($user->save()) ? true : false;
        return ['status' => $status, 'model' => $user];
    }

    /**
     * Initialize User model
     */
    public function init()
    {
        parent::init();
        $this->_statuses = [
            self::STATUS_SUPERUSER => Yii::t('user', 'Superuser'),
            self::STATUS_PENDING => Yii::t('user', 'Pending'),
            self::STATUS_ACTIVE => Yii::t('user', 'Active'),
            self::STATUS_BANNED => Yii::t('user', 'Banned'),
            self::STATUS_INACTIVE => Yii::t('user', 'Inactive'),
        ];
        $this->_statusClasses = [
            self::STATUS_SUPERUSER => 'label label-primary',
            self::STATUS_PENDING => 'label label-warning',
            self::STATUS_ACTIVE => 'label label-success',
            self::STATUS_BANNED => 'label label-danger',
            self::STATUS_INACTIVE => 'label label-default',
        ];
    }

    /**
     * User model validation rules
     *
     * @return array
     */
    public function rules()
    {
        $config = $this->_module->registrationSettings;
        $rules = [
            ['username', 'match', 'pattern' => $config['userNamePattern'], 'message' => $config['userNameValidMsg']],
            ['username', 'string'] + $config['userNameRules'],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique'],

            [['status'], 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => array_keys($this->_statuses)],

            [['password_raw'], 'required', 'on' => [Module::UI_REGISTER, Module::UI_RESET]],
            [['password_new', 'password_confirm'], 'required', 'on' => [Module::UI_RESET]],
            ['password_new', 'compare', 'compareAttribute' => 'password_confirm', 'on' => [Module::UI_RESET]],
        ];
        $strengthRules = $this->_module->passwordSettings['strengthRules'];
        $validateStrength = $this->_module->passwordSettings['validateStrength'];
        if (in_array(Module::UI_REGISTER, $validateStrength)) {
            $rules[] = [['password_raw'], StrengthValidator::className()] + $strengthRules + ['on' => [Module::UI_REGISTER]];
        }
        if (in_array(Module::UI_RESET, $validateStrength)) {
            $rules[] = [['password_new', 'password_confirm'], StrengthValidator::className()] + $strengthRules + ['on' => [Module::UI_RESET]];
        }
        return $rules;

    }

    /**
     * User model scenarios
     *
     * @return array
     */
    public function scenarios()
    {
        return [
            Module::UI_REGISTER => ['username', 'password_raw', 'email'],
            Module::UI_RESET => ['password_raw', 'password_new', 'password_confirm'],
            Module::UI_PROFILE => ['username', 'email'],
            Module::UI_ADMIN => ['username', 'email'],
            Module::UI_INSTALL => ['username', 'password_raw', 'email', 'status'],
        ];
    }

    /**
     * Attribute labels for the User model
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
            'auth_key' => Yii::t('user', 'Auth Key'),
            'activation_key' => Yii::t('user', 'Activation Key'),
            'reset_key' => Yii::t('user', 'Reset Key'),
            'status' => Yii::t('user', 'Status'),
            'created_on' => Yii::t('user', 'Created On'),
            'created_on' => Yii::t('user', 'Updated On'),
            'last_login_ip' => Yii::t('user', 'Last Login IP'),
            'last_login_on' => Yii::t('user', 'Last Login On'),
            'password_reset_on' => Yii::t('user', 'Password Reset On'),
            'password_fail_attempts' => Yii::t('user', 'Password Fail Attempts'),
            'password_raw' => Yii::t('user', 'Password'),
            'password_new' => Yii::t('user', 'New Password'),
            'password_confirm' => Yii::t('user', 'Confirm Password'),
        ];
    }

    /**
     * Mail queues relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMailQueues()
    {
        return $this->hasMany(MailQueue::className(), ['id' => 'id']);
    }

    /**
     * Remote identities relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRemoteIdentities()
    {
        return $this->hasMany(RemoteIdentity::className(), ['user_id' => 'id']);
    }

    /**
     * User ban logs relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserBanLogs()
    {
        return $this->hasMany(UserBanLog::className(), ['id' => 'id']);
    }

    /**
     * User profile relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['id' => 'id']);
    }

    /**
     * Before save event
     *
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->setAccess();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sets the access for user and configures user keys and statuses based on scenario
     */
    public function setAccess()
    {
        if ($this->scenario == Module::UI_REGISTER) {
            $this->status = self::STATUS_PENDING;
            $this->removeResetKey();
            $this->generateActivationKey();
        } elseif ($this->scenario == Module::UI_ACTIVATE || $this->scenario == Module::UI_RECOVERY) {
            $this->status = self::STATUS_ACTIVE;
            $this->password_reset_on = call_user_func($this->_module->now);
            $this->password_fail_attempts = 0;
            $this->removeResetKey();
            $this->removeActivationKey();
        } elseif ($this->scenario == Module::UI_RESET) {
            $this->status = self::STATUS_PENDING;
            $this->removeActivationKey();
            $this->generateResetKey();
        } elseif ($this->scenario == Module::UI_LOCKED) {
            $this->status = self::STATUS_INACTIVE;
            $this->removeActivationKey();
            $this->generateResetKey();
        }
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
    public function isAccountActive()
    {
        return ($this->status === self::STATUS_ACTIVE || $this->status === self::STATUS_SUPERUSER);
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
        return static::find($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token,$temp=null)
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
        return static::find(['username' => $username])->active();
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::find(['email' => $email])->active();
    }

    /**
     * Finds user by username or email
     *
     * @param string $userStr
     * @return static|null
     */
    public static function findByUserOrEmail($userStr)
    {
        return static::find()->andWhere(['username' => $userStr])->orWhere(['email' => $userStr]);
    }

    /**
     * Finds user by password reset key
     *
     * @param string $key password reset key
     * @param integer $expire password reset key expiry
     * @return static|null
     */
    public static function findByPasswordResetKey($key, $expire)
    {
        if (static::isKeyExpired($key, $expire)) {
            return null;
        }

        return static::find(['reset_key' => $key])->active();
    }

    /**
     * Check if a key is expired
     *
     * @param $key string the key
     * @param $expire integer the expiry time in seconds
     * @return bool
     */
    public static function isKeyExpired($key, $expire)
    {
        $parts = explode('_', $key);
        if (count($parts) <= 1 || empty($expire) || $expire <= 0) {
            return false;
        }
        $timestamp = (int)end($parts);
        return (($timestamp + $expire) < time());
    }

    /**
     * Generates a hash key
     *
     * @param $expire integer the expiry time in seconds
     * @return bool
     */
    public static function generateKey($expire = 0)
    {
        $key = Security::generateRandomKey();
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
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Security::validatePassword($password, $this->password);
    }

    /**
     * Validates if user is not banned and allowed to login. If ban time is expired, the user is auto activated.
     * @var bool $flag if set to `true` will auto update the user status to `STATUS_ACTIVE` if ban time expired
     * @return bool if password provided is valid for current user
     */
    public function validateBan($flag = true)
    {
        if (!$this->status !== self::STATUS_BANNED) {
            return true;
        }
        $id = $this->getLastBanID();
        if ($id > 0) {
            $banLog = UserBanLog::findOne($id);
            $expiry = $banLog ? $banLog->banned_till : null;
            if ($flag && strtotime($expiry) > time() && $this->status === self::STATUS_BANNED) {
                $this->status = self::STATUS_ACTIVE;
                if ($this->save()) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * Gets last ban id for the user
     * @return integer
     */
    public function getLastBanID() {
        return UserBanLog::find()->select('id')->andWhere(['user_id' => $this->id])->max();
    }
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Security::generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = self::generateKey($this->getAuthKeyExpiry());
    }

    /**
     * Generates new password reset key
     */
    public function generateResetKey()
    {
        $this->reset_key = self::generateKey($this->getResetKeyExpiry());
    }

    /**
     * Generates new activation key
     */
    public function generateActivationKey()
    {
        $this->activation_key = self::generateKey($this->getActivationKeyExpiry());
    }

    /**
     * Removes "remember me" authorization key
     */
    public function removeAuthKey()
    {
        $this->auth_key = null;
    }

    /**
     * Removes password reset key
     */
    public function removeResetKey()
    {
        $this->reset_key = null;
    }

    /**
     * Removes activation key
     */
    public function removeActivationKey()
    {
        $this->activation_key = null;
    }

    /**
     * Get auth key expiry
     */
    public function getAuthKeyExpiry()
    {
        if (isset($this->_authKeyExpiry)) {
            return $this->_authKeyExpiry;
        }
        $this->_authKeyExpiry = ArrayHelper::getValue($this->_module->loginSettings, 'rememberMeDuration', 2592000);
        return $this->_authKeyExpiry;
    }

    /**
     * Get reset key expiry
     */
    public function getResetKeyExpiry()
    {
        if (isset($this->_resetKeyExpiry)) {
            return $this->_resetKeyExpiry;
        }
        $this->_resetKeyExpiry = ArrayHelper::getValue($this->_module->passwordSettings, 'resetKeyExpiry', 172800);
        return $this->_resetKeyExpiry;
    }

    /**
     * Get activation key expiry
     */
    public function getActivationKeyExpiry()
    {
        if (isset($this->_activationKeyExpiry)) {
            return $this->_activationKeyExpiry;
        }
        $this->_activationKeyExpiry = ArrayHelper::getValue($this->_module->passwordSettings, 'activationKeyExpiry', 172800);
        return $this->_activationKeyExpiry;
    }

    /**
     * Get status list
     *
     * @return string
     */
    public function getStatusList()
    {
        return $this->_statuses;
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
        return '<span class="' . $this->_statusClasses[$this->status] . '">' . $this->statusName . '</span>';
    }

    /**
     * Sends an email with a link, for account activation or account recovery/reset
     *
     * @param string $type the type/template of mail to be sent
     * @return bool whether the email was sent
     */
    public function sendEmail($type)
    {
        if (!empty($this->_module->notificationSettings[$type])) {
            $content = Yii::$app->controller->renderPartial($this->_module->notificationSettings['viewPath'] . '/' . $type, ['user' => $this]);
            $settings = $this->_module->notificationSettings[$type];
            return \Yii::$app->mail
                ->compose($content)
                ->setFrom([$settings['fromEmail'] => $settings['fromName']])
                ->setTo($this->email)
                ->setSubject($settings['subject'])
                ->send();
        }
        return null;
    }
}
