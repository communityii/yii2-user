<?php


namespace commyii\user\events;

class LoginEvent extends \yii\base\Event
{
    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;
    const RESULT_LOCKED = 3;
    const RESULT_ALREADY_AUTH = 4;
    
    public $redirect;
    public $viewFile;
    public $hasSocial = false;
    public $newPassword = false;
    public $unlockExpiry;
    public $user;
    public $status;
    public $result;
    public $message;
    public $flashType;
    public $transaction;
    
}
