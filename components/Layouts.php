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
use comyii\user\components\Views;

/**
 * Class Layouts the action settings for the module.
 * 
 * @package comyii\user\components
 */
class Layouts extends ArrayComponent
{

    /**
     * @var string the name of the property to store the array items 
     */
    protected $_containerName = 'layouts';
    protected $_currentName = 'layout';

    /**
     * @var array the list of layouts 
     */
    public $layouts;

    public $layout;
    
    public $default = 'default';

    /**
     * Get the default actions
     * 
     * @return array
     */
    public function getDefaults()
    {
        return [
            Views::VIEW_LOGIN => $this->default,
            Views::VIEW_REGISTER => $this->default,
            Views::VIEW_RECOVERY => $this->default
        ];
    }
}