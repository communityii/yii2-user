<?php
use yii\helpers\Html;
use yii\helpers\Url;
use comyii\user\Module;

/* @var $this yii\web\View */
/* @var $user common\models\User */
$m = Yii::$app->getModule('user');
$action = $m->actionSettings[Module::ACTION_RESET];
$resetLink = Url::to([$action, 'key' => $user->reset_key], true);
?>

<p>Hello <b><?= Html::encode($user->username) ?></b>,</p>

<p>Follow the link below to reset your password:</p>

<blockquote><?= Html::a(Html::encode($resetLink), $resetLink) ?></blockquote>

<?php if (!empty($timeLeft)): ?>
<p><em><b>Note</b>: <?= $timeLeft ?></em></p>
<?php endif;?>

<p>Regards,</p>

<p>Administrator</p>

<p><b><?= Yii::$app->name ?></b></p>
