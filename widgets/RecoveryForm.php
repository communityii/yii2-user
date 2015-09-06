<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
use kartik\builder\Form;
use kartik\helpers\Html;
use yii\helpers\Url;
use comyii\user\Module;

/**
 * Password recovery request form widget for the yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class RecoveryForm extends BaseForm
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $m = Yii::$app->getModule('user');
        Module::validateConfig($this->_module);
        $this->attributes += [
            'email' => [
                'type' => Form::INPUT_TEXT, 
                'hint' => Yii::t('user', 'Please fill out your email to receive a link to reset your password')
            ],
        ];
        $this->leftFooter = $m->button(Module::BTN_HOME) . $m->button(Module::BTN_NEW_USER);
        $this->rightFooter = $m->button(Module::BTN_RESET_FORM) . ' ' . $m->button(Module::BTN_SUBMIT_FORM);
        parent::init();
    }
}