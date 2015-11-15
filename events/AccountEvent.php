<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\events;

/**
 * AccountEvent combines the ViewEvent and RecordEvent properties and enables access to user account related properties
 * for the communityii/yii2-user module
 */
class AccountEvent extends Event
{
    /**
     * @var Model the user model (or auth model for AuthEvent).
     */
    public $model;
    
    /**
     * @var bool whether or not to use database transactions.
     */
    public $useTransaction = true;
    
    /**
     * @var string|array the URL to be redirected to after completion of the controller action that triggered the event.
     */
    public $redirectUrl;

    /**
     * @var string|null the main view file to be rendered. If null then the default view file is used. This is used so
     *     that the view file can be changed by event handlers.
     */
    public $viewFile;

    /**
     * @var string|null the flash message for the controller. This is used so that event handlers can update the
     *     success messages for things like user registration.
     */
    public $message;

    /**
     * @var string the flash message type parsed by `Yii::$app->session->setFlash`.
     */
    public $flashType;
}
