<?php


namespace commyii\user\events;

class ExceptionEvent extends \yii\base\Event
{   
    /**
     * @var Event the event
     */
    public $event;
    /**
     * @var Exception the exception.
     */
    public $ex;
}
