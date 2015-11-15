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
 * Class PasswordEvent is used for triggering all user password events
 *
 * @package comyii\user\events\account
 */
class ResetEvent extends AccountEvent
{
    /**
     * @var bool the result of the reset attempt (read only)
     */
    public $result;
}
