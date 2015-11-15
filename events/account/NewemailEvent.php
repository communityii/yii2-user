<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\events\account;

use comyii\user\events\AccountEvent;

/**
 * Class NewEmailEvent is used for triggering all events for user email change/reset
 *
 * @package comyii\user\events\account
 */
class NewEmailEvent extends AccountEvent
{
    /**
     * @var bool the result of the reset attempt
     */
    public $result;
}
