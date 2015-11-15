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
use kartik\select2\Select2;
use comyii\user\Module;
use comyii\user\widgets\AdminMenu;
use comyii\user\models\User;

/**
 * @var View   $this
 * @var Module $m
 * @var User   $model
 */
$m = Yii::$app->getModule('user');
$actions = $m->actionSettings;
$this->title = Yii::t('user', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => [$actions[Module::ACTION_ADMIN_INDEX]]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-header">
    <div class="pull-right"><?= AdminMenu::widget(['ui' => 'create', 'user' => $model]) ?></div>
    <h1><?= $this->title ?></h1>
</div>
<?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL, 'formConfig' => ['labelSpan' => 4]]); ?>
<?= $form->errorSummary($model) ?>
<!-- dummy inputs to prevent auto fill -->
<input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;"/>
<input type="password" name="password_fake" id="password_fake" value="" style="display:none;"/>
<div class="row">
    <div class="col-md-8">
        <?php
        echo $form->field($model, 'username')->textInput(['maxlength' => 128, 'autocomplete' => 'new_username']);
        if (in_array(Module::SCN_ADMIN, $m->passwordSettings['strengthMeter'])) {
            echo $form->field($model, 'password')->widget(PasswordInput::classname(), [
            ]);
        } else {
            echo $form->field($model, 'password')->passwordInput();
        }
        echo $form->field($model, 'email')->textInput(['maxlength' => 255]);
        echo $form->field($model, 'status')->widget(Select2::classname(), [
            'data' => $m->getEditStatuses(),
            'options' => ['options' => $m->getDisabledStatuses()]
        ]);
        ?>
    </div>
</div>
<hr>
<div class="text-right">
    <?= $m->button(Module::BTN_RESET_FORM) ?>
    <?= $m->button(Module::BTN_SUBMIT_FORM) ?>
</div>
<?php ActiveForm::end(); ?>
