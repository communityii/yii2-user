<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\social;

use yii\authclient\clients\GoogleOAuth;

class GoogleAuth extends GoogleOAuth
{
    use ClientTrait;

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        /**
         * @var \yii\authclient\BaseClient $this
         */
        $attributes = $this->getUserAttributes();
        return $attributes && isset($attributes['email'][0]['value']) ? $attributes['email'][0]['value'] : null;
    }

}
