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

use comyii\user\Module;
use yii\helpers\ArrayHelper;

/**
 * Trait UserTypeTrait method used for merging userType configs in components.
 * 
 * @package comyii\user\components
 */
trait UserTypeTrait
{

    public function mergeUserTypeConfig($config = array(), $component = '')
    {
        $userTypes = &Module::getInstance()->userTypes;
        if (isset($userTypes->{$component})) {
            if (!empty($config)) {
                $userTypes->{$component} = ArrayHelper::merge($config, $userTypes->{$component});
            } else {
                $config = $userTypes->{$component};
            }
        }
        return $config;
    }
}