<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\events;

/**
 * Profile trait used in profile events.
 */
trait ProfileEventTrait 
{
    /**
     * @var \comyii\user\models\UserProfile the user's profile if has one.
     */
    public $profile;
    
    /**
     * @var \comyii\user\models\SocialProfile the user's social connect if enabled.
     */
    public $social;
    
    public function extract($vars)
    {
        /**
         * @var User          $model
         * @var UserProfile   $profile
         * @var SocialProfile $social
         */
        extract($vars);
        $this->model = $model;
        $this->profile = $profile;
        $this->social = $social;
    }
}
