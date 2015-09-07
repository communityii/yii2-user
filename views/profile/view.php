<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\detail\DetailView;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\widgets\UserMenu;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\User $model
 */

$m = $this->context->module;
$this->title =  Yii::t('user', 'Profile') . ' (' . $model->username . ')';
$this->params['breadcrumbs'][] = $model->username;
$profileSettings = $m->profileSettings;
$socialSettings = $m->socialSettings;
$hasSocial = $socialSettings['enabled'];
$hasProfile = $profileSettings['enabled'];
$socialDetails = '';

// basic attributes from User model
$accountAttribs = [
    [
        'group'=>true,
        'label'=> $m->icon('tag') . ' ' . Yii::t('user', 'Account Details'),
        'rowOptions'=>['class'=>'info']
    ],
    'username',
    'email:email',
    [
        'attribute' => 'created_on',
        'label' => Yii::t('user', 'Registered On'),
        'format' => 'datetime',
        'labelColOptions' => ['style' => 'width:' . ($hasSocial || $hasProfile ? '40' : '20') . '%;text-align:right']
    ],
    [
        'attribute' => 'last_login_on',
        'format' => 'datetime',
        'value' => strtotime($model->last_login_on) ? $model->last_login_on : null,
    ]
];

// basic attributes from SocialProfile model
$socialAttribs = [];
if ($hasSocial) {
    $socialAttribs = [
        [
            'group' => true,
            'label' => $m->icon('globe') . ' ' . Yii::t('user', 'Social Details'),
            'rowOptions' => ['class'=>'info']
        ],
    ];
    if (count($social) === 1 && $social[0]->isNewRecord) {
        $socialAttribs[] = [
            'group' => true,
            'label' => '<span class="not-set" style="font-weight:normal">' . Yii::t('user', 'No social profiles linked yet') . '</span>'
        ];
    } else {
        foreach($social as $model) {
            $socialAttribs[] = [
                'label' => empty($model->source) ? Yii::t('user', 'Provider Source') : $model->source,
                'value' => $model->source_id
            ];
        }
    }
}

// basic attributes from Profile model
$profileAttribs = [
    [
        'group' => true,
        'label' => $m->icon('user') . ' ' . Yii::t('user', 'Profile Details'),
        'rowOptions' => ['class'=>'info']
    ],
    'display_name',
    'first_name',
    'last_name',
    [
        'attribute' => 'updated_on',
        'format' => 'datetime',
        'labelColOptions' => ['style' => 'width:40%;text-align:right']
    ]
];

// render social details
if ($hasSocial) {
    $socialDetails = DetailView::widget([
        'model' => $profile,
        'striped' => false,
        'hover' => true, 
        'enableEditMode' => false,
        'attributes' => $socialAttribs
    ]);
}
?>
<div class="page-header">
    <div class="pull-right"><?= UserMenu::widget(['ui' => 'view', 'user' => $model->id]) ?></div>
    <h1><?= $this->title ?></h1>
</div>
<?php if (!$hasProfile): ?>
    <div class="row">
        <div class="col-md-<?= $hasSocial ? 6 : 12 ?>">
            <?= DetailView::widget([
                'model' => $model,
                'striped' => false,
                'hover' => true, 
                'enableEditMode' => false,
                'attributes' => $accountAttribs
            ]) ?>
        </div>
        <?php if ($hasSocial): ?>
        <div class="col-md-6">
            <?= $socialDetails ?>
        </div>
        <?php endif;?>
    </div>
<?php else: ?>
    <div class="row">      
        <div class="col-md-2 text-center">
            <?= Html::img($profile->avatarUrl, [
                'alt' => Yii::t('user', 'Avatar'), 
                'style' => 'width:160px;margin-bottom:20px',
                'class' => 'img-thumbnail'
            ]) ?>
        </div>   
        <div class="col-md-10">
            <div class="row">   
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $profile,
                        'striped' => false,
                        'hover' => true, 
                        'enableEditMode' => false,
                        'attributes' => $profileAttribs
                    ]) ?>
                    <?= $socialDetails ?>
                </div>
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'striped' => false,
                        'hover' => true, 
                        'enableEditMode' => false,
                        'attributes' => $accountAttribs
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>