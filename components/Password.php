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
use comyii\user\Module;

/**
 * Class Password the password settings component for the module.
 * 
 * @package comyii\user\components
 */
class Password extends Component
{
    /**
     * @var array|bool the list of scenarios where password strength will be validated for
     * current password. If set to `false` or an empty array, no strength will be validated. The strength will be
     * validated using `\kartik\password\StrengthValidator`. Defaults to `[Module::SCN_INSTALL, Module::SCN_REGISTER, Module::SCN_ADMIN_CREATE]`.
     */
    public $validateStrengthCurr = [Module::SCN_INSTALL, Module::SCN_REGISTER, Module::SCN_ADMIN_CREATE];
    
    /**
     * @var array|bool the list of scenarios where password strength will be validated for new
     * password. If set to `false` or an empty array, no strength will be validated. The strength will be validated
     * using `\kartik\password\StrengthValidator`. Defaults to `[Module::SCN_RESET, Module::SCN_CHANGEPASS, Module::SCN_EXPIRY]`.
     */
    public $validateStrengthNew = [Module::SCN_RESET, Module::SCN_CHANGEPASS, Module::SCN_EXPIRY];
    
    /**
     * @var array the strength validation rules as required by `\kartik\password\StrengthValidator`
     */
    public $strengthRules = [
                'min' => 8,
                'upper' => 1,
                'lower' => 1,
                'digit' => 1,
                'special' => 0,
                'hasUser' => true,
                'hasEmail' => true
            ];
    
    /**
     * @var array|bool the list of scenarios where password strength meter will be displayed.
     * If set to `false` or an empty array, no strength meter will be displayed.  Defaults to
     * `[self::SCN_REGISTER, self::SCN_RESET]`.
     */
    public $strengthMeter = [Module::SCN_RESET, Module::SCN_CHANGEPASS];
    
    /**
     * @var integer|bool the time in seconds after which the account activation key/token will
     * expire. Defaults to 3600*24*2 seconds (2 days). If set to `0` or `false`, the key never expires.
     */
    public $activationKeyExpiry = 172800; // 2 days
    
    /**
     * @var integer|bool the time in seconds after which the password reset key/token will expire.
     * Defaults to 3600*24*2 seconds (2 days). If set to `0` or `false`, the key never expires.
     */
    public $resetKeyExpiry = 172800; // 2 days
    
    /**
     * @var integer|bool the timeout in seconds after which user is required to reset his password
     * after logging in. Defaults to 90 days. If set to `0` or `false`, the password never expires.
     */
    public $passwordExpiry = 7776000; // 90 days
    
    /**
     * @var integer|bool the number of consecutive wrong password type attempts, at login, after which
     * the account is inactivated and needs to be reset. Defaults to `5`. If set to `0` or `false`, the account
     * is never inactivated after any wrong password attempts.
     */
    public $wrongAttempts = 5;
    
    /**
     * @var bool whether password recovery is permitted. If set to `true`, users will be given an option
     * to reset/recover a lost password. Defaults to `true`.
     */
    public $enableRecovery = true;
}
