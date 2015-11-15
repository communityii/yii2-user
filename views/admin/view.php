<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\helpers\Html;
use yii\web\View;
use kartik\detail\DetailView;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\widgets\AdminMenu;

/**
 * @var View   $this
 * @var Module $m
 * @var User   $model
 * @var mixed  $settings
 */
$m = Yii::$app->getModule('user');
$url = [$m->actionSettings[Module::ACTION_ADMIN_INDEX]];
$this->title = Yii::t('user', 'Manage User') . ' (' . $model->username . ')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Manage Users'), 'url' => $url];
$this->params['breadcrumbs'][] = $model->username;
$list = Html::a($m->icon('list'), $url, [
    'class' => 'kv-action-btn', 'data-toggle' => 'tooltip', 'title' => Yii::t('user', 'View users listing')
]);
$editSettings = $m->getEditSettingsAdmin($model);
$getKey = function ($key) use ($model) {
    $settings = [
        'attribute' => $key,
        'displayOnly' => true,
        'format' => 'raw',
        'value' => $model->$key ? '<samp>' . $model->$key . '</samp>' : null
    ];
    return $settings;
};
$statusHtml = $model->getStatusHtml();
$attribs1 = [
    [
        'group' => true,
        'label' => $m->icon('tag') . ' ' . Yii::t('user', 'Account Details'),
        'rowOptions' => ['class' => 'info']
    ],
    'id',
    'username',
    'email:email',
    [
        'attribute' => 'status',
        'format' => 'raw',
        'value' => empty($model->status_sec) ? $statusHtml : $statusHtml . ' ' . $model->getStatusSecHtml()
    ],
    [
        'attribute' => 'created_on',
        'format' => ['datetime', $m->datetimeDispFormat],
        'labelColOptions' => ['style' => 'width:40%;text-align:right']
    ],
];
$attribs2 = [
    [
        'group' => true,
        'label' => $m->icon('time') . ' ' . Yii::t('user', 'User Log Information'),
        'rowOptions' => ['class' => 'info'],
    ],
    [
        'attribute' => 'updated_on',
        'format' => ['datetime', $m->datetimeDispFormat],
    ],
    [
        'attribute' => 'last_login_ip',
        'format' => 'raw',
        'value' => $model->last_login_ip ? '<samp>' . $model->last_login_ip . '</samp>' : null,
    ],
    [
        'attribute' => 'last_login_on',
        'value' => Module::displayAttrTime($model, 'last_login_on'),
        'format' => ['datetime', $m->datetimeDispFormat],
        'labelColOptions' => ['style' => 'width:40%;text-align:right']
    ],
    [
        'attribute' => 'password_reset_on',
        'value' => Module::displayAttrTime($model, 'password_reset_on'),
        'format' => ['datetime', $m->datetimeDispFormat],
    ],
    'password_fail_attempts'
];
$attribs3 = null;
if ($m->checkSettings($editSettings, 'showHiddenInfo')) {
    $attribs3 = [
        [
            'group' => true,
            'label' => $m->icon('lock') . ' ' . Yii::t('user', 'Hidden Information'),
            'rowOptions' => ['class' => 'info']
        ],
        $getKey('password_hash'),
        $getKey('auth_key'),
        $getKey('email_change_key'),
        $getKey('reset_key'),
        $getKey('activation_key'),
    ];
}
?>
    <div class="page-header">
        <div class="pull-right"><?= AdminMenu::widget(['ui' => 'manage', 'user' => $model]) ?></div>
        <h1><?= $this->title ?></h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'striped' => false,
                'hover' => true,
                'enableEditMode' => false,
                'attributes' => $attribs1
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'striped' => false,
                'hover' => true,
                'enableEditMode' => false,
                'attributes' => $attribs2
            ]) ?>
        </div>
    </div>
<?php
if ($attribs3 !== null) {
    echo DetailView::widget([
        'model' => $model,
        'striped' => false,
        'hover' => true,
        'enableEditMode' => false,
        'attributes' => $attribs3
    ]);
}
?>