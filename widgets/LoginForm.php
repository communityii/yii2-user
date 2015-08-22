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
use kartik\helpers\Html;
use kartik\form\ActiveForm;
use kartik\builder\Form;
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
     * @var array HTML attributes for the reset link
     */
    public $resetLinkOptions = [
        'class' => 'text-warning pull-left',
        'data-toggle'=>'tooltip',
        'style' => 'margin-top: 7px;'
    ];

    /**
     * @var bool has social authorization
     */
    public $hasSocialAuth = false;

    /**
     * @var string the social authorization action
     */
    public $loginTitle = '';

    /**
     * @var string the social authorization action
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
        if (!isset($this->resetLinkOptions['title'])) {
            $this->resetLinkOptions['title'] = $m->message('reset-password-title');
        }
        Module::validateConfig($this->_module);
        $this->attributes += [
            'username' => ['type' => Form::INPUT_TEXT],
            'password' => ['type' => Form::INPUT_PASSWORD],
            'rememberMe' => ['type' => Form::INPUT_CHECKBOX]
        ];
        $resetLink = Html::a($m->message('reset-password-label'), Url::to($this->_module->actionSettings[Module::ACTION_RECOVERY]), $this->resetLinkOptions);
        if (!isset($this->buttons)) {
            $this->buttons = $resetLink . '&nbsp; {submit}';
        }
        $this->submitButtonOptions += [
            'label' => Yii::t('user', 'Login'),
            'icon' => 'log-in',
            'class' => 'btn btn-primary',
        ];
        if ($this->hasSocialAuth) {
            $social = AuthChoice::widget([
                 'baseAuthUrl' => [$this->authAction],
                 'popupMode' => false,
            ]);
            $this->template = <<< HTML
<div class="row">
    <div class="col-sm-7">
        <legend>{$this->loginTitle}</legend>
        {fields}
    </div>
    <div class="col-sm-5 y2u-social-clients">
        <legend>{$this->authTitle}</legend>
        {$social}
    </div>
</div>
{buttons}
HTML;
        } else {
            $this->template = "<legend>{$this->loginTitle}</legend>\n{fields}\n{buttons}";
        }
        parent::init();
    }
}