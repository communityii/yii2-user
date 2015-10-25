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

class LoginEvent extends Event
{
    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;
    const RESULT_LOCKED = 3;
    const RESULT_ALREADY_AUTH = 4;
    const RESULT_EXPIRED = 5;

    /**
     * @var boolean has social authentication
     */
    public $hasSocialAuth = false;

    /**
     * @var string|array the social authentication action. If not set, defaults to `Module::ACTION_SOCIAL_AUTH` set
     * within `Module::actionSettings`.
     * 
     */
    public $authAction;

    /**
     * @var boolean is an account unlock attempt after expiry.
     */
    public $unlockExpiry = false;

    /**
     * @var boolean whether the password has been reset.
     */
    public $newPassword = false;

    /**
     * @var string the account status. Should be one of the `Module::STATUS` constants.
     * @see \commyii\user\Module
     */
    public $status;

    /**
     * @var string the login section title
     */
    public $loginTitle;

    /**
     * @var string the social auth login section title
     */
    public $authTitle;

    /**
     * @var integer result of the login attempt. Should be one of the `LoginEvent::RESULT` constants.
     */
    public $result;
}
