<?php
/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use comyii\user\Module;
use comyii\user\models\User;

/**
 * Model for the login form
 *
 * @property string $username
 * @property string $password
 * @property string $rememberMe
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    private $_settings = [];

    /**
     * @var Module the module instance
     */
    private $_module;

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
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // check if valid password
            ['password', 'validatePassword'],
        ];
        if ($this->_settings['loginType'] === Module::LOGIN_EMAIL) {
            $rules += ['username', 'email'];
        }
        return $rules;
    }

    /**
     * Attribute labels for the User model
     *
     * @return array
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
            'rememberMe' =>  Yii::t('user', 'Remember Me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        $user = $this->getUser();
        $outcome = ($user) ? $user->validatePassword($this->$attribute) : null;
        if (!$user || !$outcome) {
            $this->addError($attribute, Yii::t('user', 'Invalid login credentials'));
        }
        if ($outcome !== null) {
            $user->checkFailedLogin($outcome);
        }

    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @param $user the user model
     * @return boolean whether the user is logged in successfully
     */
    public function login($user)
    {
        return Yii::$app->getUser()->login($user, $this->rememberMe ? $this->_settings['rememberMeDuration'] : 0);
    }

    /**
     * Finds user by [[username]], [[email]], or both
     *
     * @return User|null
     */
    public function getUser()
    {
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
}
