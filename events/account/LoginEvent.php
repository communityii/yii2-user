<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\events\account;

use comyii\user\events\AccountEvent;

/**
 * Class LoginEvent is used for triggering all user login events
 *
 * @package comyii\user\events\account
 */
class LoginEvent extends AccountEvent
{
    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;
    const RESULT_LOCKED = 3;
    const RESULT_ALREADY_AUTH = 4;
    const RESULT_EXPIRED = 5;

    /**
     * @var bool has social authentication
     */
    public $hasSocialAuth = false;

    /**
     * @var string is social authentication.
     */
    public $authAction;

    /**
     * @var bool whether the password has been reset.
     */
    public $newPassword = false;

    /**
     * @var bool is account unlock attempt.
     */
    public $unlockExpiry = false;

    /**
     * @var string the account status. Should be one of the `Module::STATUS` constants.
     * @see \commyii\user\Module
     */
    public $status;

    /**
     * @var string the login page title
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
