<?php


namespace commyii\user\events;

class LoginEvent extends \yii\base\Event
{
    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;
    const RESULT_LOCKED = 3;
    const RESULT_ALREADY_AUTH = 4;
    
    /**
     * @var string|array the URL to be redirected to.
     */
    public $redirect;
    /**
     * @var string|null the main view file to be rendered. If null then the default view file is used.
     */
    public $viewFile;
    /**
     * @var boolean has social authentication 
     */
    public $hasSocial = false;
    /**
     * @var string is social authentication.
     */
    public $authAction;
    /**
     * @var boolean whether the password has been reset. 
     */
    public $newPassword = false;
    /**
     * @var boolean is account unlock attempt.
     */
    public $unlockExpiry;
    /**
     * @var \commyii\user\models\User the user model 
     */
    public $user;
    /**
     * @var string the account status.
     * @see \commyii\user\Module
     */
    public $status;
    /**
     * @var integer result of the login attempt.
     */
    public $result;
    /**
     * @var string the flash message 
     */
    public $message;
    /**
     * @var string the flash message type
     */
    public $flashType;
    /**
     * @var boolean whether or not to use transactions. 
     */
    public $transaction;
    /**
     * @var string login page title
     */
    public $loginTitle;
    /**
     * @var string social auth login title
     */
    public $authTitle;
    
}
