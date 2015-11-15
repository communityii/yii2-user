<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\web\View;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use comyii\user\Module;
use comyii\user\widgets\AdminMenu;
use comyii\user\models\User;

/**
 * @var View   $this
 * @var Module $m
 * @var User   $model
 * @var mixed  $settings
 */
$m = Yii::$app->getModule('user');
$actions = $m->actionSettings;
$this->title = Yii::t('user', 'Update User') . ' (' . $model->username . ')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => [$actions[Module::ACTION_ADMIN_INDEX]]];
$this->params['breadcrumbs'][] = [
    'label' => $model->username,
    'url' => [$actions[Module::ACTION_ADMIN_VIEW], 'id' => $model->id]
];
$this->params['breadcrumbs'][] = Yii::t('user', 'Update');
?>
    <div class="page-header">
        <div class="pull-right"><?= AdminMenu::widget(['ui' => 'edit', 'user' => $model]) ?></div>
        <h1><?= $this->title ?></h1>
    </div>
<?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL, 'formConfig' => ['labelSpan' => 4]]); ?>
    <div class="row">
        <div class="col-md-8">
            <?php
            if ($settings === true || $settings['changeUsername']) {
                echo $form->field($model, 'username')->textInput(['maxlength' => 128]);
            }
            if ($settings === true || $settings['changeEmail']) {
                echo $form->field($model, 'email')->textInput(['maxlength' => 255]);
            }
            if (!$model->isSuperuser()) {
                echo $form->field($model, 'status')->widget(Select2::classname(), [
                    'data' => $m->getEditStatuses(),
                    'options' => ['options' => $m->getDisabledStatuses()]
                ]);
            }
            $submit = $m->button(Module::BTN_SUBMIT_FORM, [], ['label' => Yii::t('user', 'Update')]);
            ?>
        </div>
    </div>
    <hr>
    <div class="text-right">
        <?= $m->button(Module::BTN_RESET_FORM) ?>
        <?= $m->button(Module::BTN_SUBMIT_FORM, [], ['label' => Yii::t('user', 'Update')]) ?>
    </div>
<?php ActiveForm::end(); ?>