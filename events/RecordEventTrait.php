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

use yii\base\Model;


/**
 * Record event trait for all events with ActiveRecords
 */
trait RecordEventTrait
{
    /**
     * @var Model the user model (or auth model for AuthEvent).
     */
    public $model;
    
    /**
     * @var boolean whether or not to use database transactions.
     */
    public $useTransaction = true;
}
