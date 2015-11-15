<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use comyii\user\Module;
use comyii\user\models\User;

/**
 * @var View   $this
 * @var User   $user
 * @var Module $m
 */
$m = Yii::$app->getModule('user');
$action = $m->actionSettings[Module::ACTION_NEWEMAIL];
$changeLink = Url::to([$action, 'key' => $user->email_change_key], true);
?>

<p>Hello <b><?= Html::encode($user->username) ?></b>,</p>

<p>Your email change request to <b><?= $user->email_new ?></b> was received. Follow the link below to confirm and change the email address for your account.</p>

<blockquote><?= Html::a(Html::encode($changeLink), $changeLink) ?></blockquote>

<?php if (!empty($timeLeft)): ?>
    <p><em><b>Note</b>: <?= $timeLeft ?></em></p>
<?php endif;?>

<p>Regards,

<p>Administrator</p>

<p><b><?= Yii::$app->name ?></b></p>
