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

use comyii\user\events\Event;
use comyii\user\events\ViewEventTrait;
use comyii\user\events\RecordEventTrait;

class PasswordEvent extends Event
{
    use ViewEventTrait;
    use RecordEventTrait;
}
