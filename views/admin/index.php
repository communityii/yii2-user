<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\View;
use kartik\helpers\Html;
use kartik\grid\GridView;
use kartik\select2\Select2;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\models\UserSearch;
use comyii\user\assets\AdminAsset;
use comyii\user\widgets\AdminMenu;

/**
 * @var View               $this
 * @var Module             $m
 * @var User               $model
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch         $searchModel
 */

$m = Yii::$app->getModule('user');
$this->title = Yii::t('user', 'Manage Users');
$this->params['breadcrumbs'][] = $this->title;
$class = $m->modelSettings[Module::MODEL_USER];
$config = Json::encode([
    'confirmMsg' => Yii::t('user', 'Batch update statuses for all selected users?'),
    'alert1' => Yii::t('user', 'No users have been selected for batch update of status.'),
    'alert2' => Yii::t('user', 'You have not chosen a status for update.'),
    'elOut' => '#batch-status-out',
    'url' => Url::to(['batch-update'])
]);
$this->registerJs("var kvBatchUpdateConfig = {$config};", View::POS_HEAD);
AdminAsset::register($this);
?>
<div class="page-header">
    <div class="pull-right"><?= AdminMenu::widget(['ui' => 'list', 'user' => null]) ?></div>
    <h1><?= $this->title ?></h1>
</div>
<div id="batch-status-out"></div>
<div style="width:200px;float:right;margin:-10px auto;">
    <?= Select2::widget([
        'name' => 'batch-status',
        'value' => '',
        'data' => $m->getValidStatuses(),
        'addon' => [
            'append' => [
                'content' => Html::button(Html::icon('saved'), [
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
    ]) ?>
</div>
<div class="clearfix"></div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pjax' => true,
    'panel' => false,
    'export' => false,
    'options' => ['id' => 'user-grid'],
    'columns' => [
        [
            'attribute' => 'id',
            'width' => '80px',
        ],
        [
            'attribute' => 'username',
            'width' => '120px',
            'format' => 'raw',
            'content' => function ($model) {
                /**
                 * @var User $model
                 */
                return $model->getUserLink();
            }
        ],
        'email:email',
        [
            'attribute' => 'status',
            'format' => 'raw',
            'hAlign' => 'center',
            'content' => function ($model) {
                /**
                 * @var User $model
                 */
                return $model->getStatusHtml();
            },
            'filter' => $m->getPrimaryStatuses(),
            'width' => '140px',
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['placeholder' => Yii::t('user', 'Select...')],
                'pluginOptions' => ['allowClear' => true]
            ]
        ],
        [
            'attribute' => 'status_sec',
            'format' => 'raw',
            'hAlign' => 'center',
            'content' => function ($model) {
                /**
                 * @var User $model
                 */
                return $model->getStatusSecHtml();
            },
            'filter' => $m->getSecondaryStatuses(),
            'width' => '140px',
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['placeholder' => Yii::t('user', 'Select...')],
                'pluginOptions' => ['allowClear' => true]
            ]
        ],
        [
            'attribute' => 'last_login_ip',
            'format' => 'raw',
            'hAlign' => 'center',
            'width' => '130px',
            'value' => function ($model) {
                return $model->last_login_ip ? '<samp>' . $model->last_login_ip . '</samp>' : null;
            }
        ],
        [
            'attribute' => 'last_login_on',
            'format' => ['datetime', $m->datetimeDispFormat],
            'hAlign' => 'center',
            'filter' => false,
            'mergeHeader' => true,
            'value' => function ($model) {
                return Module::displayAttrTime($model, 'last_login_on');
            }
        ],
        [
            'attribute' => 'created_on',
            'hAlign' => 'center',
            'format' => 'date',
            'label' => Yii::t('user', 'Member Since'),
            'filter' => false,
            'mergeHeader' => true
        ],
        [
            'class' => 'kartik\grid\CheckboxColumn',
            'checkboxOptions' => function ($model) {
                if ($model->status == Module::STATUS_SUPERUSER) {
                    return ['disabled' => 'disabled'];
                }
            }
        ]
    ],
]); ?>
