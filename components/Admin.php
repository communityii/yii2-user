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
 * Class Admin the admin account settings for the module.
 * 
 * @package comyii\user\components
 */
class Admin extends Component
{
    /**
     * @var bool whether to allow superuser to create users. Defaults to `true`
     */
    public $createUser = true;
    
    /**
     * @var bool allow username to be changed for superuser. Defaults to `false`.
     * If set to `true`, can be changed only by the user who is the superuser.
     */
    public $changeUsername = false;
    
    /**
     * @var bool allow email to be changed for superuser. Defaults to `false`.
     * If set to `true`, can be changed only by the user who is the superuser.
     */
    public $changeEmail = false;
    
    /**
     * @var bool allow password to be reset for superuser. Defaults to `false`.
     * If set to `true`, can be reset only by the user who is the superuser.
     */
    public $resetPassword = false;
    
    /**
     * @var bool whether to show hidden information of password hash and keys
     * generated . Defaults to `true`.
     */
    public $showHiddenInfo = true;
    
}
