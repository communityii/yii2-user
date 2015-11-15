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
use comyii\user\Module;

/**
 * Password reset request form
 */
class RecoveryForm extends Model
{
    /**
     * @var string the email address
     */
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $m = Yii::$app->getModule('user');
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => $m->modelSettings[Module::MODEL_USER],
                'filter' => ['status' => Module::STATUS_ACTIVE],
                'message' => Yii::t('user', 'There is no user registered with the email!')
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('user', 'Registered Email'),
        ];
    }
}
