<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
use kartik\helpers\Html;
use kartik\builder\Form;
use kartik\password\PasswordInput;
use comyii\user\Module;

/**
 * Login form widget for the yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class LoginForm extends BaseForm
{
    /**
     * @var bool has social authorization
     */
    public $hasSocialAuth = false;

    /**
     * @var string the social authorization title
     */
    public $authTitle = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $m = $this->_module;
        if ($this->model->scenario !== Module::SCN_EXPIRY) {
            if ($this->model->scenario !== Module::SCN_REGISTER) {
                $this->mergeAttributes([
                    'username' => ['type' => Form::INPUT_TEXT],
                    'password' => ['type' => Form::INPUT_PASSWORD],
                    'rememberMe' => ['type' => Form::INPUT_CHECKBOX]
                ]);
            }
            $this->leftFooter = $m->button(Module::BTN_FORGOT_PASSWORD) . $m->button(Module::BTN_NEW_USER);
            $this->rightFooter = $m->button(Module::BTN_LOGIN);
            if ($this->hasSocialAuth && $m->socialSettings['widgetEnabled']) {
                $social = $m->getSocialWidget();
                if (!isset($this->template)) {
                    $this->template = <<< HTML
<div class="row">
    <div class="col-sm-8">
        <legend>{$this->title}</legend>
        {fields}
    </div>
    <div class="col-sm-4">
        <legend>{$this->authTitle}</legend>
        {$social}
    </div>
</div>
{footer}
HTML;
                } else {
                    $this->template = str_replace('{social}', $social, $this->template);
                }
            }
        } else {
            $password = ['type' => Form::INPUT_PASSWORD];
            if (in_array(Module::SCN_EXPIRY, $m->passwordSettings['strengthMeter'])) {
                $password = [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => PasswordInput::classname(),
                    'options' => [
                        'options' => [
                            'placeholder' => Yii::t('user', 'Password'),
                            'autocomplete' => 'new-password'
                        ]
                    ]
                ];
            }
            $this->mergeAttributes([
                'username' => ['type' => Form::INPUT_HIDDEN],
                'password' => ['type' => Form::INPUT_PASSWORD],
                'password_new' => $password,
                'password_confirm' => ['type' => Form::INPUT_PASSWORD],
            ]);
            $this->leftFooter = Html::hiddenInput('unlock-account', '1', ['id' => 'unlock-account']);
            $this->rightFooter = $m->button(Module::BTN_SUBMIT_FORM);
        }
    }
}