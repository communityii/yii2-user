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

use Yii;
use comyii\user\components\UserTypeTrait;
use comyii\common\components\ArrayComponent as BaseArrayComponent;

/**
 * Class Layouts the action settings for the module.
 * 
 * @package comyii\user\components
 */
class ArrayComponent extends BaseArrayComponent
{
    use UserTypeTrait;

    public function __construct($config = array())
    {
        $config = $this->mergeUserTypeConfig($this->mergeConfig($config));
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
        $this->init();
    }
}