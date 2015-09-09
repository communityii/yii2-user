<?php

use yii\helpers\ArrayHelper;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\widgets\UserMenu;
use kartik\helpers\Html;
use kartik\form\ActiveForm;
use kartik\file\FileInput;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\User $model
 */

$m = $this->context->module;
$this->title =  Yii::t('user', 'Update Profile') . ' (' . $model->username . ')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', $model->username), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('user', 'Update');
$profileSettings = $m->profileSettings;
$socialSettings = $m->socialSettings;
$hasSocial = $socialSettings['enabled'];
$hasProfile = $profileSettings['enabled'];
$socialDetails = '';
?>
<div class="page-header">
    <div class="pull-right"><?= UserMenu::widget(['ui' => 'edit', 'user' => $model->id]) ?></div>
    <h1><?= $this->title ?></h1>
</div>
<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data']
]); ?>
<?php if (!$hasProfile): ?>
    <?php if ($model->hasErrors()): ?>
    <?= $form->errorSummary($model) ?>
    <?php endif; ?>
    <div class="row">   
        <div class="col-md-6">
            <?= $this->render('_user', ['form' => $form, 'module' => $m, 'model' => $model]) ?>
        </div>
        <?php if ($hasSocial): ?>
        <?php endif; ?>
    </div>
<?php else: ?>
<?php 
    if (empty($profile->avatar)) {
        $delete = '';
    } else {
        $delete = Html::a(Html::icon('trash'), [$m->actionSettings[Module::ACTION_AVATAR_DELETE], 'user' => $model->username], [
            'class' => 'btn btn-danger', 
            'data-method' => 'post',
            'data-confirm' => Yii::t('user', 'Are you sure you want to delete your avatar?'),
            'title' => Yii::t('user', 'Remove avatar image')
        ]);
    }
    $widgetOptions = array_replace_recursive($m->profileSettings['widgetAvatar'], [
        'model' => $profile,
        'attribute' => 'image',
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'elErrorContainer' => '#user-avatar-errors',
            'layoutTemplates' => ['main2' => "{preview} {$delete} {remove} {browse}"],
            'defaultPreviewContent' =>  Html::img($profile->avatarUrl, [
                'alt' => Yii::t('user', 'Avatar'), 
                'style' => 'width:180px;margin-bottom:20px'
            ])
        ]
    ]);
    $css = ".user-avatar{text-align:center}
        .user-avatar .file-preview-thumbnails{width:180px;margin:auto;left:0;right:0}
        .user-avatar .file-preview-frame,.user-avatar .file-preview-frame:hover{border:none;box-shadow:none}";
    $this->registerCss($css);
?>
    <?php if ($model->hasErrors() || $profile->hasErrors()): ?>
    <?= $form->errorSummary([$model, $profile]) ?>
    <?php endif; ?>
    <div id="user-avatar-errors" style="display:none"></div>
    <div class="row">      
        <div class="col-md-3 text-center">
            <?= FileInput::widget($widgetOptions) ?>
        </div>   
        <div class="col-md-9">
            <div class="row">   
                <div class="col-md-6">
                    <?= $this->render('_profile', ['form' => $form, 'profile' => $profile]) ?>
                </div>
                <div class="col-md-6">
                    <?= $this->render('_user', ['form' => $form, 'module' => $m, 'model' => $model]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>
<hr>
<div class="text-right">
    <?= $m->button(Module::BTN_RESET_FORM) . ' ' . $m->button(Module::BTN_SUBMIT_FORM, ['label' => Yii::t('user', 'Save')]) ?>
</div>
<?php ActiveForm::end(); ?>