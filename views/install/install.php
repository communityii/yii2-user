<?php

use communityii\user\Module;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var communityii\user\models\InstallForm $model
 * @var kartik\widgets\ActiveForm $form
 */
$model->action = Module::UI_INSTALL;
?>
<p class="text-success text-center"><?= Yii::t('user', 'Access code validated! Setup a superuser to finish the install.') ?></p>
<?php $form = ActiveForm::begin(); ?>
<div class="y2u-box">
    <div class = "y2u-padding">
        <div class="page-header">
            <h3><?= Yii::t('user', 'Setup Superuser') ?>
                <small><?= Yii::t('user', 'Step 2 of 2') ?></small>
            </h3>
        </div>
        <?= $form->field($model, 'username') ?>
        <?php
        if ($this->context->module->passwordSettings['strengthMeter']) {
            echo $form->field($model, 'password')->widget('\kartik\password\PasswordInput');
        }
        else {
            echo $form->field($model, 'password')->passwordInput();
        }
        ?>
        <?= $form->field($model, 'password_confirm')->passwordInput() ?>
        <?= $form->field($model, 'email') ?>
        <?= Html::activeHiddenInput($model, 'action') ?>
    </div>
    <div class="y2u-box-footer">
        <?= Html::a('&laquo; ' . Yii::t('user', 'Back'), ['install/index'], ['class' => 'btn btn-danger pull-left']) ?>
        <?= Html::resetButton('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('user', 'Reset'), ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> ' . Yii::t('user', 'Finish'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>