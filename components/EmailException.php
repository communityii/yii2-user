<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\components;

use yii\base\Exception;
use comyii\user\events\ExceptionEvent;

/**
 * EmailException is used for triggering email related exceptions.
 */
class EmailException extends Exception
{
    /**
     * @var ExceptionEvent the exception event raised
     */
    public $event;

    /**
     * @inheritdoc
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        $this->message = $message;
        $this->event = new ExceptionEvent;
        $this->event->error = true;
        $this->event->exception = $this;
    }
}
