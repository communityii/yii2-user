<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

/* @var $this yii\web\View */
/* @var $user common\models\User */
$m = Yii::$app->getModule('user');
?>
<h3><?= $event->message ?></h3>
<?= $exception->getTraceAsString(); ?>
<p><b><?= Yii::$app->name ?></b></p>
