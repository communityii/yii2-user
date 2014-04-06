<?php
/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use kartik\password\StrengthValidator;
use communityii\user\Module;

/**
 * Installation Form model for the module
 *
 * @property string $access_code
 * @property string $username
 * @property string $password
 * @property string $password_confirm
 * @property string $email
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class InstallForm extends Model
{
    /**
     * @var string installation access code
     */
    public $access_code;

    /**
     * @var string the superuser name
     *
     */
    public $username;

    /**
     * @var string the superuser password
     */
    public $password;

    /**
     * @var string superuser password confirmation field
     */
    public $password_confirm;

    /**
     * @var string superuser email address
     *
     */
    public $email;

    /**
     * @var string the action for the install that will derive the scenario
     */
    public $action;

    /**
     * Initialize InstallForm model
     */
    public function init()
    {
        $module = null;
        Module::validateConfig($module);
        parent::init();
    }

    /**
     * InstallForm model validation rules
     *
     * @return array
     */
    public function rules()
    {
        $config = $this->_module->registrationSettings;
        $rules = [
            ['access_code', 'required', 'on' => [Module::UI_ACCESS]],
            ['access_code', 'checkAccess', 'on' => [Module::UI_ACCESS]],
            [['username', 'email'], 'filter', 'filter' => 'trim', 'on' => [Module::UI_INSTALL]],
            ['email', 'email', 'on' => [Module::UI_INSTALL]],
            [['username', 'password', 'password_confirm', 'email'], 'required', 'on' => [Module::UI_INSTALL]],
            [['username'], 'match', 'pattern' => $config['userNameLength'], 'message' => $config['userNameValidMsg'], 'on' => [Module::UI_INSTALL]],
            [['username'], 'length', $config['length'], 'on' => [Module::UI_INSTALL]],
            ['password', 'compare', 'compareAttribute' => 'password_confirm', 'on' => [Module::UI_INSTALL]],
            [['password'], StrengthValidator::className()] + $strengthRules + ['on' => [Module::UI_INSTALL]]
        ];
        if (in_array(Module::UI_INSTALL, $this->_module->passwordSettings['validateStrength'])) {
            $rules[] = [['password'], StrengthValidator::className()] +
                $this->_module->passwordSettings['strengthRules'] +
                ['on' => [Module::UI_INSTALL]];
        }
        return $rules;
    }

    /**
     * Checks the access_code against the `installAccessCode` in the module configuration.
     * This is the 'checkAccess' validator as declared in rules().
     */
    public function checkAccess()
    {
        if ($this->_module->installAccessCode !== $this->access_code) {
            $this->addError('access_code', Yii::t('user', 'The installation access code entered is incorrect.'));
        }
        $userComponent = Yii::$app()->get('user');
        if (!$userComponent instanceof \communityii\user\components\User) {
            $this->addError('access_code', Yii::t('user', 'You have not setup a valid class for your user component in your application configuration file. ' .
                'The class must extend <code>\communityii\user\components\User</code>. Class currently set: <code>{class}</code>.',
                ['class' => $userComponent::classname()]
            ));
        }
    }

    /**
     * InstallForm model scenarios
     *
     * @return array
     */
    public function scenarios()
    {
        return [
            Module::UI_ACCESS => ['access_code', 'action'],
            Module::UI_INSTALL => ['username', 'password', 'email', 'password_confirm', 'action'],
        ];
    }

    /**
     * Attribute labels for the InstallForm model
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'access_code' => Yii::t('user', 'Install Access Code'),
            'username' => Yii::t('user', 'Superuser Username'),
            'password' => Yii::t('user', 'Superuser Password'),
            'password_confirm' => Yii::t('user', 'Superuser Password Confirm'),
            'email' => Yii::t('user', 'Superuser Email'),
        ];
    }
}
