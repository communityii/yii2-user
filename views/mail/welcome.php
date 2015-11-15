<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\helpers\Html;
use yii\helpers\Url;
use comyii\user\Module;

/**
 * @var yii\web\View            $this
 * @var comyii\user\models\User $user
 * @var comyii\user\Module      $m
 */
$m = Yii::$app->getModule('user');
$action = $m->actionSettings[Module::ACTION_ACTIVATE];
$activateLink = Url::to([$action, 'key' => $user->auth_key], true);
$name = Yii::$app->name;
?>

<p>Hello <b><?= Html::encode($user->username) ?></b>,</p>

<p>Thank you for registering at <b><?= $name ?></b>. Your account has been activated. </p>

<p>Regards,</p>

<p>Administrator</p>

<p><b><?= Yii::$app->name ?></b></p>
