<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\social;

/**
 * Base trait for authorization clients used in communityii/yii2-user
 *
 * @package comyii\user\social
 */
trait ClientTrait
{
    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        /**
         * @var \yii\authclient\BaseClient $this
         */
        $attributes = $this->getUserAttributes();
        return $attributes && isset($attributes['email']) ? $attributes['email'] : null;
    }
    
    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return null;
    }
}
