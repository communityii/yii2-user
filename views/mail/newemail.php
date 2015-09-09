<?php
use yii\helpers\Html;
use yii\helpers\Url;
use comyii\user\Module;

/* @var $this yii\web\View */
/* @var $user common\models\User */
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
