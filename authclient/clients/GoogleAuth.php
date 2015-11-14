<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\authclient\clients;

use yii\authclient\clients\GoogleOAuth;

class GoogleAuth extends GoogleOAuth
{
    /** @inheritdoc */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['emails'][0]) ? $this->getUserAttributes()['emails'][0]['value'] : null;
    }
    
    /** @inheritdoc */
    public function getUsername()
    {
        return;
    }
}