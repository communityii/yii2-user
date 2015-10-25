<?php


namespace commyii\user\events;

class NewemailEvent extends \yii\base\Event
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
     * @var string|null the main view file to be rendered. If null then the default view file is used.
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
    /**
     * @var boolean the result of the reset attempt
     */
    public $result;
    /**
     * @var boolean whether or not to use transactions.
     */
    public $transaction = false;
}
