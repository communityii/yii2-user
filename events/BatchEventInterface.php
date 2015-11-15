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
 * Interface BatchEventInterface enforces methods for batch update of user account & profile model records.
 *
 * @package comyii\user\events
 */
interface BatchEventInterface
{
    public function batch();
    public function updateModel($model);
}
