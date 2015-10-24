<?php

namespace commyii\user\events;

class RegistrationEvent extends \yii\base\Event
{
    /**
     *
     * @var Model the user model
     */
    public $model;
    /**
     * @var string the type of registration. This is used if there are multiple registration types (ie. different user types)
     */
    public $type;
    /**
     * @var string|null the main view file to be rendered. If null then the default view file is used. This is used so that the view file
     * can be changed by event handlers.
     */
    public $viewFile;
    /**
     * @var string|array the URL to be redirected to.
     */
    public $redirect;
    /**
     * @var boolean the current status for the controller. This is used so that event handlers can tell the controller whether to not to continue.
     */
    public $error = false;
    /**
     * @var string|null the flash message for the controller. This is used so that event handlers can update the success messages for things like user registration.
     */
    public $message;
    /**
     * @var string the flash message type
     */
    public $flashType;
    /**
     * @var boolean whether or not to activate the user account 
     */
    public $activate;
    /**
     * @var boolean the current user activation status. 
     */
    public $isActivated = false;
}