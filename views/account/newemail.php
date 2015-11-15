<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use kartik\form\ActiveForm;
use comyii\user\Module;

/**
 * @var Module $m
 */
$m = Yii::$app->getModule('user');
$this->title = Yii::t('user', 'Confirm Email Change') . ' (' . $model->username . ')';

?>
    <div class="page-header">
        <h1><?= $this->title ?></h1>
    </div>
<?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL, 'formConfig' => ['labelSpan' => 4]]); ?>
    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'email')->staticInput()->label(Yii::t('user', 'From Email')) ?>
            <?= $form->field($model, 'email_new')->staticInput()->label(Yii::t('user', 'To Email')) ?>
            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => Yii::t('user', 'Enter account password')
            ]) ?>
        </div>
    </div>
    <hr>
    <div class="text-right">
        <?= $m->button(Module::BTN_SUBMIT_FORM) ?>
    </div>
<?php ActiveForm::end(); ?>