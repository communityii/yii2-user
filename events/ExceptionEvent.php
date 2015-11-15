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

use Exception;
use yii\web\Controller;

/**
 * ExceptionEvent is raised whenever any user exception is triggered in this module.
 */
class ExceptionEvent extends Event
{
    /**
     * @var Event the event
     */
    public $event;

    /**
     * @var Controller the controller object
     */
    public $controller;

    /**
     * @var Exception the exception object instance
     */
    public $exception;
}
