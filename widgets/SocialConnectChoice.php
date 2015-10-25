<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use yii\authclient\widgets\AuthChoice;

/**
 * Render various social authentication clients for the yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class SocialConnectChoice extends AuthChoice
{
    /**
     * @var array the HTML attributes for the container
     */
    public $options = ['class' => 'y2u-social-connects'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->view->registerCss('.y2u-social-connects .auth-clients{margin:0;padding:0}');
        parent::init();
    }
}
