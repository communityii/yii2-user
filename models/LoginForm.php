<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\models;

use Yii;
use yii\base\Model;
use comyii\user\Module;

/**
 * Model for the login form
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class LoginForm extends Model
{
    /**
     * @var string the username
     */
    public $username;

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
     * @var bool whether to remember the user
     */
    public $rememberMe = true;

    /**
     * @var Module the module instance
     */
    private $_module;

    /**
     * @var array the settings
     */
    private $_settings = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        Module::validateConfig($this->_module);
        $this->_settings = $this->_module->loginSettings;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            // username and password are both required
            [['username', 'password', 'password_new', 'password_confirm'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // check if valid password
            ['password', 'validatePassword'],
            // validate password confirmation
            ['password_confirm', 'compare', 'compareAttribute' => 'password_new'],
            // validate password confirmation
            ['password_new', 'compare', 'operator' => '!=', 'compareAttribute' => 'password'],
        ];
        if ($this->_settings['loginType'] === Module::LOGIN_EMAIL) {
            $rules += ['username', 'email'];
        }
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $label = Yii::t('user', 'Username or Email');
        if ($this->_settings['loginType'] === Module::LOGIN_USERNAME) {
            $label = Yii::t('user', 'Username');
        } elseif ($this->_settings['loginType'] === Module::LOGIN_EMAIL) {
            $label = Yii::t('user', 'Email');
        }
        return [
            'username' => $label,
            'password' => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember Me'),
            'password_new' => Yii::t('user', 'New Password'),
            'password_confirm' => Yii::t('user', 'Confirm Password')
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword($attribute)
    {
        if ($this->hasErrors()) {
            return;
        }
        $user = $this->getUser();
        if ($user === null) {
            $this->addError('username', Yii::t('user', 'Could not validate the credentials'));
            return;
        }
        if ($user->status_sec == Module::STATUS_LOCKED) {
            return;
        }
        $outcome = $user ? $user->validatePassword($this->$attribute) : null;
        if ($outcome !== null && !$outcome) {
            $user->checkFailedLogin();
        }
        $attempts = $this->_module->passwordSettings['wrongAttempts'];
        if (!$user || !$outcome) {
            if (empty($attempts)) {
                $this->addError($attribute, Yii::t('user', 'The password is incorrect'));
            } else {
                $this->addError(
                    $attribute,
                    Yii::t('user', 'The password is incorrect. {n, plural, one{One try} other{# tries}} left.', [
                        'n' => ($attempts - (int)$user->password_fail_attempts)
                    ])
                );
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @param User $user the user model
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login($user)
    {
        if (!empty($user->status_sec)) {
            return (int)$user->status_sec;
        }
        if ($user->status_sec == Module::STATUS_EXPIRED || $user->status_sec == Module::STATUS_LOCKED) {
            return $user->status_sec;
        }
        if ($user->isPasswordExpired()) {
            $user->status_sec = Module::STATUS_EXPIRED;
            $user->save(false);
            return Module::STATUS_EXPIRED;
        }
        if ($user->isLocked()) {
            $user->status_sec = Module::STATUS_LOCKED;
            $user->save(false);
            return Module::STATUS_LOCKED;
        }
        return Yii::$app->user->login($user, $this->rememberMe ? $this->_settings['rememberMeDuration'] : 0);
    }

    /**
     * Finds user by [[username]], [[email]], or both
     *
     * @return User|null
     */
    public function getUser()
    {
        /**
         * @var User $class
         */
        $class = $this->_module->modelSettings[Module::MODEL_USER];
        $loginType = $this->_settings['loginType'];
        if ($loginType === Module::LOGIN_USERNAME) {
            $user = $class::findByUsername($this->username);
        } elseif ($loginType === Module::LOGIN_EMAIL) {
            $user = $class::findByEmail($this->username);
        } else {
            $user = $class::findByUserOrEmail($this->username);
        }
        return $user;
    }

    /**
     * User model scenarios
     *
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[Module::SCN_LOGIN] = ['username', 'password', 'email', 'rememberMe'];
        $scenarios[Module::SCN_EXPIRY] = ['username', 'password', 'password_new', 'password_confirm'];
        return $scenarios;
    }
}
