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

use comyii\user\events\Event;
use comyii\user\events\ViewEventTrait;
use comyii\user\events\RecordEventTrait;

class RegistrationEvent extends Event
{
    use ViewEventTrait;
    use RecordEventTrait;
    
    /**
     * @var string the type of registration. This is used if there are multiple registration types 
     *     (i.e. different user types)
     */
    public $type;

    /**
     * @var boolean whether or not to activate the user account
     */
    public $activate = false;

    /**
     * @var boolean the current user activation status.
     */
    public $isActivated = false;
}