<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\social;

use yii\authclient\BaseClient;

/**
 * Class Client enforces additional methods for client authentication for yii2-user module
 *
 * @package comyii\user\social
 */
class Client extends BaseClient
{
    use ClientTrait;
}

