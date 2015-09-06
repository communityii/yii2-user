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

    private $_user = false;
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
        $outcome = ($user) ? $user->validatePassword($this->password) : null;
        if (!$user || !$outcome) {
            $this->addError($attribute, $this->_module->message('login-invalid'));
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
        $user->setScenario('default');
        return Yii::$app->user->login($user, $this->rememberMe ? $this->_settings['rememberMeDuration'] : 0);
    }

    /**
     * Finds user by [[username]], [[email]], or both
     *
     * @return User|null
     */
    public function getUser($scenario = 'default')
    {
        if ($this->_user === false) {
            if ($this->_settings['loginType'] === Module::LOGIN_USERNAME) {
                $user = User::findByUsername($this->username);
            } elseif ($this->_settings['loginType'] === Module::LOGIN_EMAIL) {
                $user = User::findByEmail($this->username);
            } else {
                $user = User::findByUserOrEmail($this->username);
            }
            $this->_user = $user;
        }
        $this->_user->scenario = $scenario;
        return $this->_user;
    }
}
