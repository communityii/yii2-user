<?php

use comyii\user\widgets\LoginForm;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\Login $model
 */
?>
<div class="y2u-box">
    <h2 style="padding:0 15px;">Please sign in</h2>
    <?= LoginForm::widget([
        'model' => $model,
        'options' => ['options' => ['class' => 'y2u-padding']],
        'formOptions' => ['fieldConfig' => ['autoPlaceholder' => true]],
        'buttonsContainer' => ['class' => 'y2u-box-footer']
    ]); ?>
</div>