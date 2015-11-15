<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\IdentityInterface;
use kartik\password\StrengthValidator;
use comyii\user\Module;
use derekisbusy\haikunator\Haikunator;

/**
 * This is the model class for table {{%user}}.
 *
 * @property string      $id
 * @property string      $username
 * @property string      $email
 * @property string      $email_new
 * @property string      $password_hash
 * @property string      $auth_key
 * @property string      $activation_key
 * @property string      $reset_key
 * @property string      $email_change_key
 * @property integer     $status
 * @property integer     $status_sec
 * @property integer     $created_on
 * @property integer     $updated_on
 * @property string      $last_login_ip
 * @property string      $last_login_on
 * @property string      $password_reset_on
 * @property string      $password_fail_attempts
 * @property string      $password write-only password
 * @property string      $password_new write-only password
 * @property string      $password_confirm write-only password
 * @property string      $captcha the captcha for registration
 * @property string      $type the type of user or null if not implementing user types.
 *
 * @property UserProfile $profile
 * @property string      statusText
 * @property string      statusSecText
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class User extends BaseModel implements IdentityInterface
{
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
     * Table name for the User model
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
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
            [
                'username',
                'unique',
                'targetClass' => self::classname(),
                'message' => Yii::t('user', 'This username has already been taken')
            ],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [
                'email',
                'unique',
                'targetClass' => self::classname(),
                'message' => Yii::t('user', 'This email address is already registered')
            ],
            ['password', 'required'],
            ['password', 'isValidPassword', 'on' => [Module::SCN_CHANGEPASS, Module::SCN_NEWEMAIL]],
            [['password_new', 'password_confirm'], 'required', 'on' => [Module::SCN_RESET, Module::SCN_CHANGEPASS]],
            [
                'password_confirm',
                'compare',
                'compareAttribute' => 'password_new',
                'on' => [Module::SCN_RESET, Module::SCN_CHANGEPASS]
            ],
            [
                'password_new',
                'compare',
                'compareAttribute' => 'password',
                'operator' => '!=',
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
     */
    public function isValidPassword($attribute)
    {
        if ($this->hasErrors()) {
            return;
        }
        if (!$this->validatePassword($this->$attribute)) {
            $this->addError($attribute, Yii::t('user', 'Invalid password entered'));
        }

    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[Module::SCN_ACTIVATE] = ['status'];
        $scenarios[Module::SCN_REGISTER] = ['username', 'password', 'email', 'captcha', 'status'];
        $scenarios[Module::SCN_RESET] = ['password_new', 'password_confirm'];
        $scenarios[Module::SCN_CHANGEPASS] = ['password', 'password_new', 'password_confirm'];
        $scenarios[Module::SCN_INSTALL] = ['username', 'password', 'email', 'status'];
        $scenarios[Module::SCN_NEWEMAIL] = ['password'];
        $scenarios[Module::SCN_RECOVERY] = ['reset_key'];
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
        $currUser = Yii::$app->user;
        if ($currUser->isSuperuser || $currUser->isAdmin) {
            $settings = $currUser->isSuperuser ? $m->superuserEditSettings : $m->adminEditSettings;
            if (is_array($settings) && $settings['createUser']) {
                $scenarios[Module::SCN_ADMIN_CREATE] = ['username', 'password', 'email', 'status'];
            }
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $status = Yii::t('user', 'Status');
        $statusSec = Yii::t('user', 'Secondary Status');
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
            'status_sec' => $statusSec,
            'statusSecText' => $statusSec,
            'statusSecHtml' => $statusSec,
            'fullName' => Yii::t('user', 'Full Name'),
            'created_on' => Yii::t('user', 'Created On'),
            'updated_on' => $this->scenario == Module::SCN_CHANGEPASS ? Yii::t('user', 'Last Updated On') :
                Yii::t('user', 'Updated On'),
            'last_login_ip' => Yii::t('user', 'Last Login IP'),
            'last_login_on' => Yii::t('user', 'Last Login On'),
            'password_reset_on' => Yii::t('user', 'Password Reset On'),
            'password_fail_attempts' => Yii::t('user', 'Password Fail Attempts'),
            'password' => $this->scenario == Module::SCN_CHANGEPASS ? Yii::t('user', 'Current Password') :
                Yii::t('user', 'Password'),
            'password_new' => Yii::t('user', 'New Password'),
            'password_confirm' => Yii::t('user', 'Confirm Password')
        ];
    }

    /**
     * Get user details link
     *
     * @param bool $showId whether user id is shown (if false assumes username)
     *
     * @return string
     */
    public function getUserLink($showId = false)
    {
        $label = $showId ? $this->id : $this->username;
        $m = $this->_module;
        $url = $m->actionSettings[Module::ACTION_ADMIN_VIEW];
        return Html::a($label, [$url, 'id' => $this->id], [
            'data-pjax' => '0',
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
        $m = $this->_module;
        $limit = $m->passwordSettings['passwordExpiry'];
        if ($limit > 0) {
            $resetTime = $m->timestamp($this->password_reset_on, false);
            if ($resetTime === null) {
                return false;
            }
            $expiry = time() - $resetTime;
            return ($expiry >= $limit);
        }
        return false;
    }

    /**
     * Is this user a superuser?
     *
     * @return bool
     */
    public function isSuperuser()
    {
        return $this->status === Module::STATUS_SUPERUSER;
    }

    /**
     * Is this user an admin?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->status === Module::STATUS_SUPERUSER || $this->status === Module::STATUS_ADMIN;
    }

    /**
     * Is user account active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === Module::STATUS_ACTIVE
        || $this->status === Module::STATUS_SUPERUSER
        || $this->status === Module::STATUS_ADMIN;
    }

    /**
     * Is account locked due to failed attempts
     *
     * @return bool
     */
    public function isLocked()
    {
        $attempts = $this->_module->passwordSettings['wrongAttempts'];
        return (!empty($attempts) && $this->password_fail_attempts > $attempts);
    }

    /**
     * Validate failed login attempt
     */
    public function checkFailedLogin()
    {
        if ($this->status_sec == Module::STATUS_LOCKED) {
            return;
        }
        $attempts = $this->_module->passwordSettings['wrongAttempts'];
        if (empty($attempts)) {
            return;
        }
        $n = (int)$this->password_fail_attempts;
        if ($n < $attempts - 1) {
            $this->password_fail_attempts = $n + 1;
        } else {
            $this->status_sec = Module::STATUS_LOCKED;
        }
        $this->save(false);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->active()->one();
    }

    /**
     * Finds user by email
     *
     * @param string $email
     *
     * @return static
     */
    public static function findByEmail($email)
    {
        return static::find()->where(['email' => $email])->active()->one();
    }

    /**
     * Finds user by username or email
     *
     * @param string $input
     *
     * @return static
     */
    public static function findByUserOrEmail($input)
    {
        return static::find()->where(['username' => $input])->orWhere(['email' => $input])->active()->one();
    }

    /**
     * Finds user by either auth_key, reset_key, activation_key,
     * or email_change_key
     *
     * @param string  $attribute the key attribute name
     * @param string  $value key attribute value
     * @param integer $expire key expiry
     *
     * @return static
     */
    public static function findByKey($attribute, $value, $expire = 0)
    {
        if (!static::isKeyValid($value, $expire)) {
            return null;
        }
        if ($attribute === 'auth_key') {
            return static::find()->where([$attribute => $value])->pending()->one();
        } else {
            return static::find()->where([$attribute => $value])->active()->one();
        }
    }

    /**
     * Check if a key value is valid
     *
     * @param string  $value key value
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
        $timestamp = (int)end($parts);
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
        $this->save(false);
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
     *
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
     * Generates new email change key
     */
    public function generateEmailChangeKey()
    {
        $this->email_change_key = self::generateKey($this->getEmailChangeKeyExpiry());
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
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
        $this->password_reset_on = call_user_func($this->_module->now);
    }

    /**
     * Sets a random password
     *
     * @param string $userType the user type
     */
    public function setRandomPassword($userType = null)
    {
        /**
         * @var Module $m
         */
        $m = Yii::$app->getModule('user');
        $randomPasswordGenerator = $m->getRegistrationSetting('randomPasswordGenerator', $userType, null);
        if (is_callable($randomPasswordGenerator)) {
            $password = call_user_func($randomPasswordGenerator);
        } else {
            $minPassLen = $m->getRegistrationSetting('randomPasswordMinLength', $userType, 10);
            $maxPassLen = $m->getRegistrationSetting('randomPasswordMaxLength', $userType, 14);
            $password = Yii::$app->security->generateRandomString(rand($minPassLen, $maxPassLen));
        }
        $this->password = $password;
    }

    /**
     * Sets a random username
     *
     * @param string $userType the user type
     */
    public function setRandomUsername($userType = null)
    {
        /**
         * @var Module $m
         */
        $m = Yii::$app->getModule('user');
        $randomUsernameGenerator = $m->getRegistrationSetting('randomUsernameGenerator', $userType);
        $i = 0;
        $username = '';
        $proceed = true;
        while ($i < 100 && $proceed) {
            if (is_callable($randomUsernameGenerator)) {
                $username = call_user_func($randomUsernameGenerator);
            } else {
                $username = Haikunator::haikunate($randomUsernameGenerator);
            }
            $proceed = self::find()->where(['username' => $username])->exists();
            $i++;
        }
        $this->username = $username;
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
     * User friendly status name
     *
     * @return string
     */
    public function getStatusText()
    {
        return $this->_module->statuses[$this->status];
    }

    /**
     * Formatted status name
     *
     * @return string
     */
    public function getStatusHtml()
    {
        return '<span class="' . $this->_module->statusClasses[$this->status] . '">' . $this->statusText . '</span>';
    }

    /**
     * User friendly secondary status name
     *
     * @return string
     */
    public function getStatusSecText()
    {
        return empty($this->status_sec) ? '' : $this->_module->statuses[$this->status_sec];
    }

    /**
     * Formatted secondary status name
     *
     * @return string
     */
    public function getStatusSecHtml()
    {
        return empty($this->status_sec) ? '-' :
            '<span class="' . $this->_module->statusClasses[$this->status_sec] . '">' . $this->statusSecText . '</span>';
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
     *
     * @param string $emailOld the old email
     *
     * @return string
     */
    public function validateEmailChange($emailOld)
    {
        if ($emailOld != $this->email) {
            $this->email_new = $this->email;
            $this->email = $emailOld;
            if (!static::isKeyValid($this->email_change_key, $this->getEmailChangeKeyExpiry())) {
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
     *
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

    /**
     * Unlocks an user from locked or expired state
     */
    public function unlock()
    {
        if ($this->status_sec != Module::STATUS_LOCKED && $this->status_sec != Module::STATUS_EXPIRED) {
            return;
        }
        if ($this->status_sec == Module::STATUS_LOCKED) {
            $this->status_sec = null;
            $this->password_fail_attempts = 0;
        }
        if ($this->status_sec == Module::STATUS_EXPIRED) {
            $this->status_sec = null;
            $this->password_reset_on = call_user_func($this->_module->now);
        }
    }
}
