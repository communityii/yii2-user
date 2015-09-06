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
        Module::validateConfig($this->_module);
        $this->attributes += [
            'username' => ['type' => Form::INPUT_TEXT],
            'password' => ['type' => Form::INPUT_PASSWORD],
            'rememberMe' => ['type' => Form::INPUT_CHECKBOX]
        ];
        $this->leftFooter = $m->button(Module::BTN_FORGOT_PASSWORD) . $m->button(Module::BTN_NEW_USER);
        $this->rightFooter = $m->button(Module::BTN_LOGIN);
        if ($this->hasSocialAuth) {
            $social = AuthChoice::widget([
                 'baseAuthUrl' => [$this->authAction],
                 'popupMode' => false,
            ]);
            if (!isset($this->template)) {
                $this->template = <<< HTML
<div class="row">
    <div class="col-sm-8">
        <legend>{$this->title}</legend>
        {fields}
    </div>
    <div class="col-sm-4 y2u-social-clients">
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
    }
}