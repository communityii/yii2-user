<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\captcha\Captcha;
use kartik\builder\Form;
use kartik\password\PasswordInput;
use comyii\user\Module;

/**
 * Registration form widget for the yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class RegistrationForm extends LoginForm
{
    /**
     * @var array HTML attributes for the reset link
     */
    public $leftLinkOptions = [
        'class' => 'text-warning pull-left',
        'data-toggle' => 'tooltip',
        'style' => 'margin-top: 7px;'
    ];

    /**
     * @var bool has social authorization
     */
    public $hasSocialAuth = false;

    /**
     * @var string the social authorization title
     */
    public $authTitle = '';

    /**
     * @var mixed the social authorization action
     */
    public $authAction = 'account/auth';
    
    /**
     * @var string the user type
     */
    public $userType;

    /**
     * @inheritdoc
     */
    public function init()
    {
        /**
         * @var Module $m
         */
        $m = Yii::$app->getModule('user');
        if (!$m->getRegistrationSetting('randomUsernames', $this->userType)) {
            $this->attributes['username'] = ['type' => Form::INPUT_TEXT, 'options' => ['autocomplete' => 'new-username']];
        }
        if (!$m->getRegistrationSetting('randomPasswords', $this->userType)) {
            $password = ['type' => Form::INPUT_PASSWORD];
            if (in_array(Module::SCN_REGISTER, $m->passwordSettings['strengthMeter'])) {
                $password = [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => PasswordInput::classname(),
                    'options' => [
                        'options' => [
                            'placeholder' => Yii::t('user', 'Password'),
                            'autocomplete' => 'off',
                        ]
                    ]
                ];
            }
            $this->attributes['password'] = $password;
        }
        $this->attributes['email'] = ['type' => Form::INPUT_TEXT];
        
        $captcha = ArrayHelper::getValue($m->registrationSettings, 'captcha', false);
        if ($captcha !== false && is_array($captcha)) {
            $this->attributes['captcha'] = [
                'type' => Form::INPUT_WIDGET,
                'widgetClass' => Captcha::classname(),
                'options' => $captcha['widget']
            ];
        }
        parent::init();
        unset($this->attributes['rememberMe']);
        $this->leftFooter = $m->button(Module::BTN_HOME) . $m->button(Module::BTN_ALREADY_REGISTERED);
        $this->rightFooter = $m->button(Module::BTN_RESET_FORM) . ' ' . $m->button(Module::BTN_REGISTER);
    }
}