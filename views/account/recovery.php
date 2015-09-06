<?php

use comyii\user\widgets\RecoveryForm;
use comyii\user\widgets\Logo;

$m = $this->context->module;
$this->title = Yii::t('user', 'Password Recovery');
/**
 * @var yii\web\View $this
 * @var comyii\user\models\Login $model
 */
?>
<div class="text-center">
    <?= Logo::widget() ?>
</div>
<div class="y2u-box">
    <?= RecoveryForm::widget([
        'model' => $model,
        'title' => $this->title
    ]); ?>
</div>