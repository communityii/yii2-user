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

use comyii\user\components\ArrayComponent;

/**
 * Class Actions the action settings for the module.
 * 
 * @package comyii\user\components
 */
class Actions extends ArrayComponent
{
    // the list of account actions
    const ACTION_LOGIN = 1;             // login as new user
    const ACTION_LOGOUT = 2;            // logout of account
    const ACTION_REGISTER = 3;          // new account registration
    const ACTION_ACTIVATE = 4;          // account activation
    const ACTION_RECOVERY = 5;          // account password recovery request
    const ACTION_RESET = 6;             // account password reset
    const ACTION_CAPTCHA = 7;           // account captcha for registration
    const ACTION_NEWEMAIL = 8;          // account new email change action
    const ACTION_SOCIAL_AUTH = 15;      // social auth & login

    // the list of profile actions
    const ACTION_PROFILE_INDEX = 50;    // profile index
    const ACTION_PROFILE_VIEW = 51;     // profile view
    const ACTION_PROFILE_UPDATE = 52;   // profile update
    const ACTION_ACCOUNT_PASSWORD = 53; // profile password change
    const ACTION_AVATAR_DELETE = 54;    // profile image delete

    // the list of admin actions (applicable only for admin & superuser)
    const ACTION_ADMIN_INDEX = 100;     // user listing
    const ACTION_ADMIN_VIEW = 101;      // user view
    const ACTION_ADMIN_UPDATE = 102;    // user update
    const ACTION_ADMIN_CREATE = 103;    // user creation

    /**
     * @var string the name of the property to store the array items 
     */
    protected $_containerName = 'actions';

    /**
     * @var string the current item property 
     */
    protected $_currentName = 'action';

    /**
     * @var array the list of actions 
     */
    public $actions;

    /**
     * @var string|null the current action or null 
     */
    public $action;

    /**
     * Get the default actions
     * 
     * @return array
     */
    public function getDefaults()
    {
        return [
            // the list of account actions
            self::ACTION_LOGIN => 'account/login',
            self::ACTION_LOGOUT => 'account/logout',
            self::ACTION_REGISTER => 'account/register',
            self::ACTION_ACTIVATE => 'account/activate',
            self::ACTION_RESET => 'account/reset',
            self::ACTION_RECOVERY => 'account/recovery',
            self::ACTION_CAPTCHA => 'account/captcha',
            self::ACTION_NEWEMAIL => 'account/newemail',
            self::ACTION_SOCIAL_AUTH => 'account/auth',
            // the list of profile actions
            self::ACTION_PROFILE_INDEX => 'profile/index',
            self::ACTION_PROFILE_UPDATE => 'profile/update',
            self::ACTION_ACCOUNT_PASSWORD => 'account/password',
            self::ACTION_PROFILE_VIEW => 'profile/view',
            // the list of avatar actions
            self::ACTION_AVATAR_DELETE => 'profile/avatar-delete',
            // the list of admin actions
            self::ACTION_ADMIN_INDEX => 'admin/index',
            self::ACTION_ADMIN_VIEW => 'admin/view',
            self::ACTION_ADMIN_UPDATE => 'admin/update',
            self::ACTION_ADMIN_CREATE => 'admin/create',
        ];
    }
}