<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
use kartik\builder\Form;
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
        /**
         * @var Module $m
         */
        $m = Yii::$app->getModule('user');
        Module::validateConfig($this->_module);
        $this->attributes += [
            'email' => [
                'type' => Form::INPUT_TEXT,
                'hint' => Yii::t('user', 'Please fill out your email to receive a link to reset your password')
            ],
        ];
        $this->leftFooter = $m->button(Module::BTN_HOME, [], ['tabindex' => 1]) .
            $m->button(Module::BTN_NEW_USER, [], ['tabindex' => 2]);
        $this->rightFooter = $m->button(Module::BTN_RESET_FORM, [], ['tabindex' => 3]) . ' ' .
            $m->button(Module::BTN_SUBMIT_FORM, [], ['tabindex' => 0]);
        parent::init();
    }
}
