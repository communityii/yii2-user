<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\View;
use kartik\grid\GridView;
use kartik\select2\Select2;
use comyii\user\models\User;
use comyii\user\assets\AdminAsset;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var comyii\user\models\UserSearch $searchModel
 */

$m = $this->context->module;
$this->title = Yii::t('user', 'Manage Users');
$this->params['breadcrumbs'][] = $this->title;
$user = new User;
$allStatuses = $user->allStatusList;
$statuses = $user->statusList;
unset($user);
$config = Json::encode([
    'confirmMsg' => Yii::t('user', 'Batch update statuses for all selected users?'),
    'alert1' => Yii::t('user', 'You must select users (via checkbox) for batch update.'),
    'alert2' => Yii::t('user', 'No status selected for batch update.'),
    'url' => Url::to(['batch-update'])
]);
$this->registerJs("var kvBatchUpdateConfig = {$config};", View::POS_HEAD);
AdminAsset::register($this);

$batch = Yii::t('user', 'Batch update...');
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pjax' => true,
    'panel' => [
        'type'=>'primary',
        'heading' => $m->icon('list') . ' ' . $this->title,
    ],
    'export' => false,
    'options' => ['id' => 'user-grid'],
    'toolbar' => [
        [
            'content' => Html::a($m->icon('plus'), ['create'], [
                'class' => 'btn btn-success', 'title' => Yii::t('user', 'Create User'),
            ]) . Html::a($m->icon('refresh'), ['index'], [
                'data-pjax' => 0, 
                'class' => 'btn btn-default', 
                'title' => Yii::t('user', 'Refresh User List')
            ])      
        ],
        [
            'content' => '<div style="width:200px">' . Select2::widget([
                'name' => 'batch-status',
                'value' => '',
                'data' => $statuses,
                'addon' => [
                    'append' => [
                        'content' => Html::button($m->icon('save'),[
                            'class' => 'btn btn-default',
                            'id' => 'btn-batch-update',
                            'title' => $batch
                        ]),
                        'asButton' => true
                    ],
                ],
                'options' => [
                    'id' => 'batch-status',
                    'placeholder' => $batch
                ]
            ])  . '</div>'
        ]
    ],
    'columns' => [
        [
            'attribute'=>'id', 
            'width'=>'80px',
        ],
        [
            'attribute'=>'username', 
            'width'=>'120px', 
            'format'=>'raw', 
            'content'=>function($model) {
                return $model->getUserLink();
            }
        ],
        'email:email',
        'created_on:datetime',
        [
            'attribute' => 'last_login_on',
            'format' => 'datetime',
            'value' => function($model) {
                return strtotime($model->last_login_on) ? $model->last_login_on : null;
            }
        ],
        'last_login_ip',
        [
            'attribute' => 'status', 
            'format' => 'raw', 
            'content' => function($model) {
                return $model->getStatusHtml();
            },
            'filter' => $allStatuses,
            'width' => '145px',
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['placeholder' => Yii::t('user', 'Select...')],
                'pluginOptions' => ['allowClear'=>true]
            ]
        ],
        [
            'class'=>'kartik\grid\CheckboxColumn',
            'checkboxOptions' => function($model) {
                if ($model->status == User::STATUS_SUPERUSER) {
                    return ['disabled' => 'disabled'];
                }
            }
        ]
    ],
]); ?>
