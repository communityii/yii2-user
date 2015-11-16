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
 * Class Models the model settings for the module.
 * 
 * @package comyii\user\components
 */
class Models extends Component
{
    private $models = [
        Module::MODEL_LOGIN => 'comyii\user\models\LoginForm',
        Module::MODEL_USER => 'comyii\user\models\User',
        Module::MODEL_USER_SEARCH => 'comyii\user\models\UserSearch',
        Module::MODEL_PROFILE => 'comyii\user\models\UserProfile',
        Module::MODEL_SOCIAL_PROFILE => 'comyii\user\models\SocialProfile',
        Module::MODEL_PROFILE_SEARCH => 'comyii\user\models\UserProfileSearch',
        Module::MODEL_RECOVERY => 'comyii\user\models\RecoveryForm',
    ];
    
    /**
     * Constructor for Login component
     * 
     * @param type $config
     */
    public function __construct($config = array(), $m = null) {
        if ($m === null) {
            $m = Yii::$app->getModule('user');
        }
        if ($m->userTypes[$m->getUserType()]['models']) {
            $config = Module::mergeDefault($config, $m->userTypes[$m->getUserType()]['models']);
        }
        if (is_array($config)) {
            $this->models = array_replace_recursive($this->models, $config);
        }
    }
}

