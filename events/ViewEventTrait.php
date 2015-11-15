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
 * View event trait for all events within views
 */
trait ViewEventTrait
{
    use FlashEventTrait;
    /**
     * @var string|array the URL to be redirected to after completion of the controller action that triggered the event.
     */
    public $redirectUrl;

    /**
     * @var string|null the main view file to be rendered. If null then the default view file is used. This is used so
     *     that the view file can be changed by event handlers.
     */
    public $viewFile;
}
