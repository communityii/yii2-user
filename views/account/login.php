<?php

use comyii\user\widgets\LoginForm;
/**
 * @var yii\web\View $this
 * @var comyii\user\models\Login $model
 */
?>
<h1 class="y2u-app-title"><?= isset($this->params['logo']) ? $this->params['logo'] : '' ?><?= Yii::$app->name ?></h1>
<div class="y2u-box">
    <?= LoginForm::widget([
        'model' => $model,
        'options' => ['options' => ['class' => 'y2u-padding']],
        'formOptions' => ['fieldConfig' => ['autoPlaceholder' => true]],
        'loginTitle' => $loginTitle,
        'buttonsContainer' => ['class' => 'y2u-box-footer'],
        'hasSocialAuth' => $hasSocialAuth,
        'authAction' => $authAction,
        'authTitle' => $authTitle
    ]); ?>
</div>