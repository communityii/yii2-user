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

/**
 * Class Scenarios the scenarios for the module.
 * 
 * @package comyii\user\components
 */
class Scenarios
{
    // the major model scenarios (some of these map to specific user interfaces)
    const SCN_ACCESS = 'access';
    const SCN_INSTALL = 'install';
    const SCN_LOGIN = 'login';
    const SCN_REGISTER = 'register';
    const SCN_ACTIVATE = 'activate';
    const SCN_RESET = 'reset';
    const SCN_CHANGEPASS = 'password';
    const SCN_RECOVERY = 'recovery';
    const SCN_LOCKED = 'locked';
    const SCN_EXPIRY = 'expiry';
    const SCN_PROFILE = 'profile';
    const SCN_ADMIN = 'admin';
    const SCN_ADMIN_CREATE = 'adminCreate';
    const SCN_NEWEMAIL = 'newemail';
}