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

use yii\authclient\clients\Facebook;

/**
 * Facebook authorization client for `communityii/yii2-user` module
 */
class FacebookAuth extends Facebook
{
    use ClientTrait;
}
