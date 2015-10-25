<?php


namespace commyii\user\events;

class AuthEvent extends \yii\base\Event
{
    const RESULT_LOGGED_IN = 1;
    const RESULT_DUPLICATE_EMAIL = 2;
    const RESULT_SIGNUP_ERROR = 3;
    const RESULT_SIGNUP_SUCCESS = 4;
    const RESULT_AUTH_ERROR = 5;
    
    /**
     * @var \yii\authclient\BaseClient the client
     */
    public $client;
    /**
     * @var \yii\db\ActiveRecord the current social auth
     */
    public $auth;
    /**
     * @var string the user class
     */
    public $userClass;
    /**
     * @var string the social class
     */
    public $socialClass;
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
}
