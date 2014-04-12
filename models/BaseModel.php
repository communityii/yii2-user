<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\models;

use communityii\user\Module;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the base model class for all active record models
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BaseModel extends ActiveRecord
{
    /* Current module */
    protected $_module;

    /**
     * Initialize the model for the user module
     */
    public function init()
    {
        Module::validateConfig($this->_module);
        parent::init();
    }

    /**
     * Model behaviors
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_on', 'updated_on'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_on'],
                ],
                'value' => $this->_module->now,
            ],
        ];
    }

}
