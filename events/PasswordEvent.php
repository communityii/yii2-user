<?php


namespace commyii\user\events;

class PasswordEvent extends \yii\base\Event
{
    /**
     * @var Model the user model
     */
    public $model;
    /**
     * @var string|array the URL to be redirected to.
     */
    public $redirect;
    /**
     * @var string|array the view file to be rendered.
     */
    public $viewFile;
    /**
     * @var string the flash message 
     */
    public $message;
    /**
     * @var string the flash message type
     */
    public $flashType;
}
