<?php

use comyii\user\widgets\LoginForm;
/**
 * @var yii\web\View $this
 * @var comyii\user\models\Login $model
 */
?>
<h1 class="y2u-app-title"><?= isset($this->params['logo']) ? $this->params['logo'] : '' ?><?= Yii::$app->name ?></h1>
<div class="y2u-box">
    <h3 class="y2u-header">Please sign in</h3>
    <?php if ($hasSocialAuth): ?>
    <div class="row"> 
        <div class="col-xs-12 col-sm-8 col-md-6">
    <?php endif; ?>
    <?= LoginForm::widget([
        'model' => $model,
        'options' => ['options' => ['class' => 'y2u-padding']],
        'formOptions' => ['fieldConfig' => ['autoPlaceholder' => true]],
        'buttonsContainer' => ['class' => 'y2u-box-footer']
    ]); ?>
    <?php if ($hasSocialAuth): ?>
        </div>
         <div class="col-xs-12 col-sm-4 col-md-6">
             <?= yii\authclient\widgets\AuthChoice::widget([
                 'baseAuthUrl' => [$authAction],
                 'popupMode' => false,
            ]) ?>
         </div>
    <?php endif; ?>
</div>