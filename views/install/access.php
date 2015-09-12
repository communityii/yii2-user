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
$model->action = Module::SCN_ACCESS;
$hints = $model->attributeHints();
$this->params['install-mode'] = true;
?>
<div class="text-center">
    <?= Logo::widget(['title' => "communityii\\yii2-user"]) ?>
</div>
<p class="text-info text-center">
    <b><?= Yii::t('user', 'Welcome!') ?></b> 
    <?= Yii::t('user', 'You are 2 steps away to start using the yii2-user module.') ?>
</p>
<?php $form = ActiveForm::begin(); ?>
<div class="y2u-box">
    <div class = "y2u-padding">
        <div class="page-header">
            <h3><?= Yii::t('user', 'Getting Started') ?>
                <small><?= Yii::t('user', 'Step 1 of 2') ?></small>
            </h3>
        </div>
        <?= $form->field($model, 'access_code')->passwordInput()->hint($hints['access_code']) ?>
        <?= Html::activeHiddenInput($model, 'action') ?>
    </div>
    <div class="y2u-box-footer">
        <?= Html::a('<i class="glyphicon glyphicon-question-sign"></i> ' . Yii::t('user', 'Help'), '#help', [
            'class' => 'btn btn-info pull-left', 'onclick' => '$("#help").slideToggle("slow");'
        ]) ?>
        <?= Html::submitButton(Yii::t('user', 'Proceed') . ' &raquo;', ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<br>
<div id="help" class="alert alert-info" style="display:none;">
    <h4><i class="glyphicon glyphicon-exclamation-sign"></i> Important</h4>
    <small>
    <ul style="padding-left: 15px">
        <li>You must have setup a module named <code>user</code> and setup an install access code for the module in your configuration file.</li>
        <li>You must have setup the user component in your configuration file to use/extend <code>comyii\user\components\User</code> class.</li>
        <li>You must have run the database migrations as mentioned in documentation.</li>
        <li>You must have setup the mailer component in your configuration file to use <code>yii\swiftmailer\Mailer</code> component.</li>
        <li>You should optionally edit the user module settings for your user and password preferences.</li>
        <li>The installer would guide you to create a superuser. Ensure you remember the superuser access credentials for future.</li>
    </ul>
    </small>
</div>
