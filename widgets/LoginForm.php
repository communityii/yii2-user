<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\widgets;

use Yii;
use yii\base\InvalidConfigException;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use communityii\user\Module;

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
    public $resetLinkOptions = ['class' => 'text-warning pull-left', 'data-toggle'=>'tooltip', 'style' => 'margin-top: 7px;'];
    public function init()
    {
        if (!isset($this->resetLinkOptions['title'])) {
            $this->resetLinkOptions['title'] = Yii::t('user', 'Click here to reset your lost password.');
        }
        Module::validateConfig($this->_module);
        $this->attributes += [
            'username' => ['type' => Form::INPUT_TEXT],
            'password' => ['type' => Form::INPUT_PASSWORD],
            'rememberMe' => ['type' => Form::INPUT_CHECKBOX]
        ];
        $resetLink = Html::a(Yii::t('user', 'Forgot password?'), Url::to($this->_module->actionSettings[Module::ACTION_RECOVERY]), $this->resetLinkOptions);
        if (!isset($this->buttons)) {
            $this->buttons = $resetLink . '&nbsp; {submit}';
        }
        $this->submitButtonOptions += [
            'label' => Yii::t('user', 'Login'),
            'icon' => 'log-in',
            'class' => 'btn btn-primary',
        ];
        parent::init();
    }
}