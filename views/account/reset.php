<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\web\View;
use kartik\form\ActiveForm;
use kartik\password\PasswordInput;
use comyii\user\Module;
use comyii\user\widgets\UserMenu;
use comyii\user\models\User;

/**
 * @var View   $this
 * @var Module $m
 * @var User   $model
 */

$m = Yii::$app->getModule('user');
$this->title = Yii::t('user', 'Reset Password') . ' (' . $model->username . ')';
?>
    <div class="page-header">
        <div class="pull-right"><?= UserMenu::widget(['ui' => 'password', 'user' => $model->id]) ?></div>
        <h1><?= $this->title ?></h1>
    </div>
<?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL, 'formConfig' => ['labelSpan' => 4]]); ?>
    <div class="row">
        <div class="col-md-8">
            <?php if (in_array(Module::SCN_RESET, $m->passwordSettings['strengthMeter'])): ?>
                <?= $form->field($model, 'password_new')->widget(PasswordInput::classname(), [
                    'pluginOptions' => ['toggleMask' => false],
                    'options' => ['placeholder' => Yii::t('user', 'Enter new password')]
                ]); ?>
            <?php else: ?>
                <?= $form->field($model, 'password_new')->passwordInput([
                    'placeholder' => Yii::t('user', 'Enter new password')
                ]) ?>
            <?php endif; ?>
            <?= $form->field($model, 'password_confirm')->passwordInput([
                'placeholder' => Yii::t('user', 'Confirm new password')
            ]) ?>
        </div>
    </div>
    <hr>
    <div class="text-right">
        <?= $m->button(Module::BTN_SUBMIT_FORM) ?>
    </div>
<?php ActiveForm::end(); ?>