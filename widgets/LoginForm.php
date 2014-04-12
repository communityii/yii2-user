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
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * Login form widget for the yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class LoginForm extends BaseForm
{
    public function init() {
        if (!isset($this->attributes)) {
            $this->attributes = [
                'username' => [
                    'type' => Form::INPUT_TEXT,
                ],
                'password' => [
                    'type' => Form::INPUT_PASSWORD,
                ],
                'rememberMe' => [
                    'type' => Form::INPUT_CHECKBOX,
                ]
            ];
        }
        parent::init();
    }
}