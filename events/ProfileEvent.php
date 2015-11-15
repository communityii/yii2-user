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

use comyii\user\models\User;
use comyii\user\models\UserProfile;
use comyii\user\models\SocialProfile;

/**
 * ProfileEvent is used for triggering all user profile related events. It combines all properties available in
 * AccountEvent with additional user profile properties and methods.
 */
class ProfileEvent extends AccountEvent
{
    /**
     * @var User the user model
     */
    public $model;

    /**
     * @var UserProfile the user's profile model if available.
     */
    public $profile;

    /**
     * @var SocialProfile the user's social connect if enabled or available.
     */
    public $social;

    /**
     * Extract the variables
     * @param array $vars
     */
    public function extract($vars = [])
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

    /**
     * Compact and return the model variables
     * @return array
     */
    public function getModels($vars = [])
    {
        return [
            'model' => $this->model,
            'profile' => $this->profile,
            'social' => $this->social
        ];
    }
}
