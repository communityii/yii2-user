<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\social;

use yii\authclient\clients\Twitter;

/**
 * Twitter authorization client for `communityii/yii2-user` module
 */
class TwitterAuth extends Twitter
{
    use ClientTrait;

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        $attributes = $this->getUserAttributes();
        return $attributes && !empty($attributes['screen_name']) ?
            static::parseUsername($attributes['screen_name']) :
            $this->getDefaultUsername();
    }
}
