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
use yii\helpers\Url;

/**
 * Class Login the login settings for the module.
 * 
 * @package comyii\user\components
 */
class Login extends Component
{
    /**
     * @var integer whether users can login with their username, email address, or both.
     * Defaults to `self::LOGIN_BOTH`.
     */
    public $loginType = Module::LOGIN_BOTH;
    
    /**
     * @var integer the duration in seconds for which user will remain logged in on their client
     * using cookies. Defaults to 3600*24*1 seconds (30 days).
     */
    public $rememberMeDuration = Module::DAYS_30;
    
    /**
     * @var string|array the default url to redirect after login. Normally the last return
     * url will be used. This setting will override this behavior and always redirect to this specified url.
     */
    public $loginRedirectUrl;
    
    /**
     * @var string|array the default url to redirect after logout. If not set, it will redirect
     * to the home page.
     */
    public $logoutRedirectUrl;
    
    /**
     * Constructor for Login component
     * 
     * @param type $config
     */
    public function __construct($config = array()) {
        $this->loginRedirectUrl = Yii::$app->user->goHome();
        $this->logoutRedirectUrl = Url::home();
        parent::__construct($config);
    }
}

