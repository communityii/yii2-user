<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model comyii\user\models\UserBanLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-ban-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_ip') ?>

    <?= $form->field($model, 'ban_reason') ?>

    <?= $form->field($model, 'revoke_reason') ?>

    <?= $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'banned_till') ?>

    <?php // echo $form->field($model, 'created_on') ?>

    <?php // echo $form->field($model, 'updated_on') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('user', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('user', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
