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
 * Class RegistrationEvent is used for triggering all user registration events
 *
 * @package comyii\user\events\account
 */
class RegistrationEvent extends AccountEvent
{
    /**
     * @var string the type of registration. This is used if there are multiple registration types
     *     (i.e. different user types)
     */
    public $type;

    /**
     * @var bool whether or not to activate the user account
     */
    public $activate = false;

    /**
     * @var bool the current user activation status.
     */
    public $isActivated = false;
}