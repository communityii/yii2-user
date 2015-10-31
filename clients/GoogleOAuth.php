<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\clients;

use yii\authclient\clients\GoogleOAuth as BaseGoogleOAuth;

class GoogleOAuth extends BaseGoogleOAuth
{
    /** @inheritdoc */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['email']) ? $this->getUserAttributes()['email'] : null;
    }
    
    /** @inheritdoc */
    public function getUsername()
    {
        return;
    }
}