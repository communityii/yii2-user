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
 * Class AuthEvent is used for triggering all user authorization events
 *
 * @package comyii\user\events\account
 */
class AuthEvent extends AccountEvent
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
     * @var string the user class
     */
    public $userClass;

    /**
     * @var string the social class
     */
    public $socialClass;
    
    /**
     * @var bool the result of the authorization attempt. Should be one of the `AuthEvent::RESULT` constants.
     */
    public $result;
}
