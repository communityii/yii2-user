<?php

use comyii\user\widgets\RegistrationForm;
use comyii\user\widgets\Logo;
/**
 * @var yii\web\View $this
 * @var comyii\user\models\Login $model
 */
?>
<div class="text-center">
    <?= Logo::widget() ?>
</div>
<div class="y2u-box">
    <?= RegistrationForm::widget([
        'model' => $model,
        'title' => $registerTitle,
        'hasSocialAuth' => $hasSocialAuth,
        'authAction' => $authAction,
        'authTitle' => $authTitle
    ]); ?>
</div>
