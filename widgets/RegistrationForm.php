<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\base\InvalidConfigException;
use yii\authclient\widgets\AuthChoice;
use yii\captcha\Captcha;
use kartik\helpers\Html;
use kartik\form\ActiveForm;
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
        'data-toggle'=>'tooltip',
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
     * @inheritdoc
     */
    public function init()
    {
        $m = Yii::$app->getModule('user');
        $captcha = '';
        $password = ['type' => Form::INPUT_PASSWORD];
        if (in_array(Module::UI_REGISTER, $m->passwordSettings['strengthMeter'])) {
            $password = [
                'type' => Form::INPUT_WIDGET, 
                'widgetClass' => PasswordInput::classname(),
                'options' => [
                    'pluginOptions' => ['toggleMask' => false],
                    'options' => [
                        'placeholder' => Yii::t('user', 'Password'), 
                        'autocomplete'=>'new-password'
                    ]
                ]
            ];
        }
        $captcha = ArrayHelper::getValue($m->registrationSettings, 'captcha', false);
        $this->attributes += [
            'username' => ['type' => Form::INPUT_TEXT, 'options' => ['autocomplete'=>'new-username']],
            'password' => $password,
            'email' => ['type' => Form::INPUT_TEXT]
        ];
        if ($captcha === false || !is_array($captcha)) {
            $captcha = '';
        } else {
            $this->attributes['captcha'] = [
                'type' => Form::INPUT_WIDGET,
                'widgetClass' => Captcha::classname(),
                'options' => $captcha['widget']
            ];
        }
        parent::init();
        $this->leftFooter = $m->button(Module::BTN_HOME) . $m->button(Module::BTN_ALREADY_REGISTERED);
        $this->rightFooter = $m->button(Module::BTN_RESET_FORM) . ' ' . $m->button(Module::BTN_REGISTER);
        unset($this->attributes['rememberMe']);
    }
}