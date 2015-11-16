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
 * Class Captcha the registration settings for the module.
 * 
 * @package comyii\user\components
 */
class Captcha extends Component
{
    /**
     * @var array the captcha action 
     */
    public $action;
    
    /**
     * @var array the captcha widget config
     */
    public $widget;
    
    /**
     * @var array the captcha validator config 
     */
    public $validator;
    
}
