<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

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
$editSettings = $m->getEditSettingsUser($model);
$socialDetails = $authClients = '';

// basic attributes from User model
$accountAttribs = [
    [
        'group'=>true,
        'label'=> $m->icon('tag') . Yii::t('user', 'Account Details'),
        'rowOptions'=>['class'=>'info']
    ],
    'username',
    'email:email',
];
if ($editSettings['changeEmail'] && !empty($model->email_new)) {
    $accountAttribs[] = 'email_new:email';
}
$accountAttribs[] = [
    'attribute' => 'created_on',
    'label' => Yii::t('user', 'Registered On'),
    'format'=>['datetime', $m->datetimeDispFormat],
    'labelColOptions' => ['style' => 'width:' . ($hasSocial || $hasProfile ? '40' : '20') . '%;text-align:right']
];
$accountAttribs[] = [
    'attribute' => 'last_login_on',
    'format'=>['datetime', $m->datetimeDispFormat],
    'value' => strtotime($model->last_login_on) ? $model->last_login_on : null,
];

// basic attributes from SocialProfile model
$socialAttribs = [];
if ($hasSocial) {
    $socialAttribs = [
        [
            'group' => true,
            'label' => $m->icon('globe') . Yii::t('user', 'Social Details'),
            'rowOptions' => ['class'=>'info']
        ],
    ];
    if (count($social) === 1 && $social[0]->isNewRecord) {
        $socialAttribs[] = [
            'group' => true,
            'label' => '<span class="not-set" style="font-weight:normal">' . Yii::t('user', 'No social profiles linked yet') . '</span>'
        ];
    } else {
        foreach($social as $record) {
            $provider = empty($record->source) ? Yii::t('user', 'Unknown') : '<span class="auth-icon ' . $record->source . '"></span>' .
                '<span class="auth-title">' . ucfirst($record->source) . '</span>';
            $socialAttribs[] = [
                'label' => Yii::t('user', 'Source'),
                'value' => '<b>' . Yii::t('user', 'Connected On') . '</b>',
                'format' => 'raw',
                'rowOptions' => ['class'=>'active'],
                'labelColOptions' => ['style' => 'width:90px;text-align:center']
            ];
            $socialAttribs[] = [
                'label' => '<div class="auth-client"><span class="auth-link">' . $provider . '</span></div>',
                'value' => $record->updated_on,
                'labelColOptions' => ['style' => 'text-align:center;vertical-align:middle'],
                'valueColOptions' => ['style' => 'vertical-align:middle'],
                'format' => ['datetime', $m->datetimeDispFormat]
            ];
        }
    }
}

// basic attributes from Profile model
$profileAttribs = [
    [
        'group' => true,
        'label' => $m->icon('user') . Yii::t('user', 'Profile Details'),
        'rowOptions' => ['class'=>'info']
    ],
    'first_name',
    'last_name',
    [
        'attribute' => 'gender',
        'format' => 'raw',
        'value' => $profile->genderHtml,
        'labelColOptions' => ['style' => 'width:40%;text-align:right']
    ],
    'birth_date:date'
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
    if (Yii::$app->user->id == $model->id) {
        $authClients = DetailView::widget([
            'model' => $profile,
            'striped' => false,
            'enableEditMode' => false,
            'attributes' => [
                [
                    'group' => true,
                    'label' => $m->icon('link') . Yii::t('user', 'Connect Social Accounts'),
                    'rowOptions' => ['class'=>'info']
                ],
                [
                    'group' => true,
                    'label' => $m->getSocialWidget()
                ]
            ]
        ]);
    }
}
$this->registerCss('.user-link-social .auth-clients {margin:4px;padding:0}');
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
                    <?= $authClients ?>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>