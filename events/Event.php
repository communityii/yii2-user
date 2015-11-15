<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\events;

/**
 * Base event for all events in communityii/yii2-user module
 */
class Event extends \yii\base\Event
{
    /**
     * @var bool the current status for the controller. This is used so that event handlers can tell the controller
     *     whether to not to continue.
     */
    public $error = false;
}
