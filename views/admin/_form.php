<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => 20]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'password_hash')->password_hashInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'activation_key')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'reset_key')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'password_fail_attempts')->textInput() ?>

    <?= $form->field($model, 'last_login_ip')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'last_login_on')->textInput() ?>

    <?= $form->field($model, 'password_reset_on')->textInput() ?>

    <?= $form->field($model, 'created_on')->textInput() ?>

    <?= $form->field($model, 'updated_on')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('user', 'Create') : Yii::t('user', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
