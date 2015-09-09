<?php

use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\View;
use kartik\helpers\Html;
use kartik\grid\GridView;
use kartik\select2\Select2;
use comyii\user\Module;
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
$class = $m->modelSettings[Module::MODEL_USER];
$user = new $class;
$allStatuses = $user->allStatusList;
$statuses = $user->statusList;
unset($statuses[User::STATUS_PENDING]);
unset($user);
$config = Json::encode([
    'confirmMsg' => Yii::t('user', 'Batch update statuses for all selected users?'),
    'alert1' => Yii::t('user', 'You must select users (via checkbox) for batch update.'),
    'alert2' => Yii::t('user', 'No status selected for batch update.'),
    'url' => Url::to(['batch-update'])
]);
$this->registerJs("var kvBatchUpdateConfig = {$config};", View::POS_HEAD);
AdminAsset::register($this);
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
                        'content' => Html::button(Html::icon('saved'),[
                            'class' => 'btn btn-default',
                            'id' => 'btn-batch-update',
                            'title' => Yii::t('user', 'Go!')
                        ]),
                        'asButton' => true
                    ],
                ],
                'options' => [
                    'id' => 'batch-status',
                    'placeholder' => Yii::t('user', 'Batch update...')
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
        [
            'attribute' => 'status', 
            'format' => 'raw', 
            'hAlign' => 'center',
            'content' => function($model) {
                return $model->getStatusHtml();
            },
            'filter' => $allStatuses,
            'width' => '140px',
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['placeholder' => Yii::t('user', 'Select...')],
                'pluginOptions' => ['allowClear'=>true]
            ]
        ],
        [
            'attribute' => 'last_login_ip',
            'format' => 'raw',
            'hAlign' => 'center',
            'width' => '140px',
            'value' => function($model) {
                return $model->last_login_ip ? '<code>' . $model->last_login_ip . '</code>' : null;
            }
        ],
        [
            'attribute' => 'last_login_on',
            'format' => 'datetime', 
            'hAlign' => 'center',
            'filter' => false,
            'mergeHeader' => true,
            'value' => function($model) {
                return strtotime($model->last_login_on) ? $model->last_login_on : null;
            }
        ],
        [
            'attribute' => 'created_on',
            'format' => 'datetime', 
            'hAlign' => 'center',
            'filter' => false,
            'mergeHeader' => true
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
