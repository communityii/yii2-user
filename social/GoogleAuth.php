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

use yii\authclient\clients\GoogleOAuth;

/**
 * Google OAuth authorization client for `communityii/yii2-user` module
 */
class GoogleAuth extends GoogleOAuth
{
    use ClientTrait;
    
    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        $attributes = $this->getUserAttributes();
        return $attributes && isset($attributes['emails'][0]['value']) ? $attributes['emails'][0]['value'] : null;
    }

}
