<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use comyii\user\Module;
use comyii\user\widgets\Logo;
use kartik\helpers\Html;
use kartik\form\ActiveForm;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\InstallForm $model
 * @var kartik\form\ActiveForm $form
 */
$m = $this->context->module;
$model->action = Module::SCN_INSTALL;
$this->params['install-mode'] = true;
?>
<div class="text-center">
    <?= Logo::widget(['title' => "communityii\\yii2-user"]) ?>
</div>
<p class="text-success text-center">
    <b><?= Yii::t('user', 'Access code validated!') ?></b> 
    <?= Yii::t('user', 'Setup a superuser to finish the install.') ?>
</p>
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
        if ($m->passwordSettings['strengthMeter']) {
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
        <?= $m->button(Module::BTN_RESET_FORM) ?>
        <?= $m->button(Module::BTN_SUBMIT_FORM) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>