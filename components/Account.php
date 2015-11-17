<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\components;

use yii\base\Component;

/**
 * Class Account the user account settings for the module.
 * 
 * @package comyii\user\components
 */
class Account extends Component
{
    /**
     * @var bool allow username to be changed for user. Defaults to `true`.
     * If set to `true`, can be changed by the respective user OR any admin/superuser via
     * admin user interface. If set to `false` change access will be disabled for all users.
     */
    public $changeUsername = true;
    
    /**
     * @var bool allow email to be changed for user. Defaults to `true`.
     *   If set to `true`, can be changed by the respective user OR any admin/superuser via
     *   admin user interface. Note that `email` change by normal users needs to be revalidated
     *   by user by following instructions via the system mail sent. If set to `false` change
     *   access will be disabled for all users.
     */
    public $changeEmail = true;
}
