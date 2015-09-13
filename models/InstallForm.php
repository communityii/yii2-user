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
use kartik\password\StrengthValidator;
use comyii\user\components\User as UserComponent;
use comyii\user\Module;

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
     * @var Module the module instance
     */
    private $_module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        Module::validateConfig($this->_module);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $config = $this->_module->registrationSettings;
        $rules = [
            [['access_code', 'username', 'password', 'email', 'password_confirm', 'action'], 'safe'],
            ['access_code', 'required', 'on' => [Module::SCN_ACCESS]],
            ['access_code', 'checkAccess', 'on' => [Module::SCN_ACCESS]],
            [['username', 'email'], 'filter', 'filter' => 'trim', 'on' => [Module::SCN_INSTALL]],
            ['email', 'email', 'on' => [Module::SCN_INSTALL]],
            [['username', 'password', 'password_confirm', 'email'], 'required', 'on' => [Module::SCN_INSTALL]],
            [
                'username',
                'match',
                'pattern' => $config['userNamePattern'],
                'message' => $config['userNameValidMsg'],
                'on' => [Module::SCN_INSTALL]
            ],
            ['username', 'string'] + $config['userNameRules'] + ['on' => Module::SCN_INSTALL],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'on' => [Module::SCN_INSTALL]],
            [
                ['password'],
                StrengthValidator::className()
            ] + $this->_module->passwordSettings['strengthRules'] + ['on' => [Module::SCN_INSTALL]]
        ];
        if (in_array(Module::SCN_INSTALL, $this->_module->passwordSettings['validateStrengthCurr'])) {
            $rules[] = [['password'], StrengthValidator::className()] +
                $this->_module->passwordSettings['strengthRules'] +
                ['on' => [Module::SCN_INSTALL]];
        }
        return $rules;
    }

    /**
     * Checks the access_code against the `installAccessCode` in the module configuration.
     * This is the 'checkAccess' validator as declared in rules().
     */
    public function checkAccess()
    {
        $m = $this->_module;
        if ($m->installAccessCode !== $this->access_code) {
            $this->addError('access_code', Yii::t('user', 'The installation access code entered is incorrect'));
        }
        $userComponent = Yii::$app->get('user');
        if (!$userComponent instanceof UserComponent) {
            $this->addError('access_code', Yii::t(
                'user',
                'You have not setup a valid class for your user component in your application configuration file. ' .
                'The class must extend {classValid}. Class currently set: {classSet}.',
                [
                    'classValid' => UserComponent::classname(),
                    'classSet' => $userComponent::classname()
                ]
            ));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'access_code' => Yii::t('user', 'Access Code'),
            'username' => Yii::t('user', 'Username'),
            'password' => Yii::t('user', 'Password'),
            'password_confirm' => Yii::t('user', 'Confirm Password'),
            'email' => Yii::t('user', 'Email')
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'access_code' => Yii::t(
                'user',
                'Enter the installation access code as setup in your module configuration.'
            ),
            'username' => Yii::t('user', 'Select an username for the superuser'),
            'password' => Yii::t('user', 'Select a password for the superuser'),
            'password_confirm' => Yii::t('user', 'Reconfirm the superuser password'),
            'email' => Yii::t('user', 'Enter a valid email address for the superuser')
        ];
    }
}
