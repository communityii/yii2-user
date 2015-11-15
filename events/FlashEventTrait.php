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
 * Flash event trait used for events with flash messages
 */
trait FlashEventTrait
{
    /**
     * @var string|null the flash message for the controller. This is used so that event handlers can update the
     * success messages for things like user registration.
     */
    public $message;

    /**
     * @var string the flash message type parsed by `Yii::$app->session->setFlash`.
     */
    public $flashType;
}
