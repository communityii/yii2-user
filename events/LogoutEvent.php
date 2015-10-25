<?php


namespace commyii\user\events;

class LogoutEvent extends \yii\base\Event
{   
    /**
     * @var string|array the URL to be redirected to.
     */
    public $redirect;
    /**
     * @var string the flash message 
     */
    public $message;
    /**
     * @var string the flash message type
     */
    public $flashType;
}
