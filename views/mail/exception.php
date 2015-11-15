<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use comyii\user\events\ExceptionEvent;

/**
 * @var ExceptionEvent $event
 * @var Exception      $exception
 */
?>
<h3><?= $event->message ?></h3>
<?= $exception->getTraceAsString(); ?>
<p><b><?= Yii::$app->name ?></b></p>
