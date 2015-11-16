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
 * Class Views the view settings for the module.
 * 
 * @package comyii\user\components
 */
class Views extends Component
{
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
