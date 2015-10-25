<?php


namespace commyii\user\events;

class ActivateEvent extends \yii\base\Event
{   
    /**
     * @var Model the user model
     */
    public $model;
    /**
     * @var string|array the URL to be redirected to. Defaults to $controller->goHome().
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
    /**
     * @var boolean the result of the activation attempt
     */
    public $result;
    /**
     * @var boolean whether or not to use transactions.
     */
    public $transaction = false;
}
