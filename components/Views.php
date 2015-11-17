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
 * Class Views the view settings for the module.
 * 
 * @package comyii\user\components
 */
class Views extends ArrayComponent
{
    use UserTypeTrait;

    /**
     * @var string the name of the property to store the array items 
     */
    protected $_containerName = 'views';

    /**
     * @var string pointer to current view in list or null
     */
    protected $_currentName = 'view';

    /**
     * @var array the list of views 
     */
    public $views;

    // the list of views used
    const VIEW_LOGIN = 200;             // login form
    const VIEW_REGISTER = 201;          // new user registration form
    const VIEW_NEWEMAIL = 202;          // new email change confirmation
    const VIEW_PASSWORD = 203;          // password change form
    const VIEW_RECOVERY = 204;          // password recovery form
    const VIEW_RESET = 205;             // password reset form
    const VIEW_ADMIN_INDEX = 206;       // manage users list (for admin & superuser only)
    const VIEW_ADMIN_CREATE = 207;      // create user form (for admin & superuser only)
    const VIEW_ADMIN_UPDATE = 208;      // update user form (for admin & superuser only)
    const VIEW_ADMIN_VIEW = 209;        // update user form (for admin & superuser only)
    const VIEW_PROFILE_INDEX = 210;     // user profile view (for current user only)
    const VIEW_PROFILE_UPDATE = 211;    // user profile update (for current user only)
    const VIEW_PROFILE_VIEW = 212;      // user profile view (for any user viewable by admin & superuser)

    public $_views = [
        // views in AccountController
        self::VIEW_LOGIN => 'login',
        self::VIEW_REGISTER => 'register',
        self::VIEW_NEWEMAIL => 'newemail',
        self::VIEW_PASSWORD => 'password',
        self::VIEW_RECOVERY => 'recovery',
        self::VIEW_RESET => 'reset',
        // views in AdminController
        self::VIEW_ADMIN_INDEX => 'index',
        self::VIEW_ADMIN_CREATE => 'create',
        self::VIEW_ADMIN_UPDATE => 'update',
        self::VIEW_ADMIN_VIEW => 'view',
        // views in ProfileController
        self::VIEW_PROFILE_INDEX => 'view',
        self::VIEW_PROFILE_UPDATE => 'update',
        self::VIEW_PROFILE_VIEW => 'view',
    ];
    
    public function __get($name) {
        if (isset($this->_views[$name])) {
            return $this->_views[$name];
        }
    }
}
