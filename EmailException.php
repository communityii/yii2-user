<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user;

use comyii\user\events\ExceptionEvent;

class EmailException extends \yii\base\Exception
{
    public $event;
    
	public function __construct ($message, $code, $previous) {
        $this->message = $message;
        $this->event = new ExceptionEvent;
        $this->event->error = true;
        $this->event->exception = $this;
    }
    
}