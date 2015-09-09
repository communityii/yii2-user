<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\detail\DetailView;
use comyii\user\Module;
use comyii\user\models\User;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\User $model
 */

$m = $this->context->module;
$url = [$m->actionSettings[Module::ACTION_ADMIN_LIST]];
$this->title =  Yii::t('user', 'Manage User') . ' (' . $model->username . ')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Manage Users'), 'url' => $url];
$this->params['breadcrumbs'][] = $model->username;
$list = Html::a($m->icon('list'), $url, ['class'=>'kv-action-btn', 'data-toggle'=>'tooltip', 'title' => Yii::t('user', 'View users listing')]);
$editSettings = $m->getEditSettingsAdmin($model);
$getKey = function($key, $flag = true) use ($model) {
    $settings = [
        'attribute' => $key, 
        'displayOnly' => true, 
        'format' => 'raw', 
        'value' => $model->$key ? '<kbd>' . $model->$key . '</kbd>' : null
    ];
    if ($flag) {
        $settings['valueColOptions'] = ['style' => 'width: 30%'];
    }
    return $settings;
};
$passActions = $m->button(Module::BTN_ADMIN_RESET, ['id' => $model->id], [
    'disabled' => is_array($editSettings) ? !$editSettings['resetPassword'] : true
]);
if ($model->id == Yii::$app->user->id) {
    $passActions .= ' ' . $m->button(Module::BTN_CHANGE_PASSWORD);
}
?>
<?= DetailView::widget([
    'model' => $model,
    'striped' => false,
    'hover' => true,
    'buttons1' => "{$list}{update}",
    'panel' => [
        'type' => 'primary',
        'heading' => $m->icon('user') . ' ' . $this->title,
    ],
    'buttonContainer'=>['style'=>'margin-right:-10px;float:right'],
    'attributes' => [
        [
            'group'=>true,
            'label'=> $m->icon('tag') . ' ' . Yii::t('user', 'Identification Information'),
            'rowOptions'=>['class'=>'info']
        ],
        [
            'columns' => [
                [
                    'attribute'=> 'id',
                    'displayOnly' => true,
                    'valueColOptions' => ['style' => 'width: 30%']
                ], 
                [
                    'attribute' => 'username',
                    'displayOnly' => is_array($editSettings) ? !$editSettings['changeUsername'] : !$editSettings,
                    'valueColOptions' => ['style' => 'width: 30%']
                ],
            ]
        ],
        [
            'columns' => [
                [
                    'attribute'=> 'status', 
                    'format' => 'html',
                    'value'=>$model->statusHtml, 
                    'type' => DetailView::INPUT_SELECT2,
                    'displayOnly' => $model->isAccountSuperuser(),
                    'valueColOptions' => ['style' => 'width: 30%'],
                    'widgetOptions'=>[
                        'data' => $model->getStatusList(),
                        'pluginOptions' => ['width' => '100%'],
                        'options' => [
                            'options' => [
                                User::STATUS_PENDING => ['disabled' => true]
                            ]
                        ]
                    ]
                ],
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'displayOnly' => is_array($editSettings) ? !$editSettings['changeEmail'] : !$editSettings,
                    'valueColOptions' => ['style' => 'width: 30%']
                ],    
            ],
        ],
        [
            'label' => Yii::t('user', 'User Details'),
            'value' => $m->button(Module::BTN_PROFILE_VIEW, ['id' => $model->id]),
            'displayOnly' => true,
            'format' => 'raw'
        ],
        [
            'group'=>true,
            'label'=> $m->icon('time') . ' ' . Yii::t('user', 'User Log Information'),
            'rowOptions'=>['class'=>'info'],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'last_login_ip', 
                    'format' => 'raw',
                    'value' => $model->last_login_ip ? '<code>' . $model->last_login_ip . '</code>' : null,
                    'valueColOptions' => ['style' => 'width: 30%'], 
                    'displayOnly' => true
                ],
                [
                    'attribute'=> 'created_on', 
                    'format'=>['datetime', $m->datetimeFormat], 
                    'valueColOptions' => ['style' => 'width: 30%'], 
                    'displayOnly' => true
                ],
            ]
        ],
        [
            'columns' => [
                [
                    'attribute'=> 'last_login_on', 
                    'format'=>['datetime', $m->datetimeFormat], 
                    'value' => strtotime($model->last_login_on) ? $model->last_login_on : null,
                    'valueColOptions' => ['style' => 'width: 30%'], 'displayOnly' => true
                ],
                [
                    'attribute'=> 'updated_on', 
                    'format'=>['datetime', $m->datetimeFormat], 
                    'valueColOptions' => ['style' => 'width: 30%'], 
                    'displayOnly' => true
                ],
            ]
        ],
        [
            'columns' => [
                [
                    'attribute'=> 'password_reset_on',
                    'value' => strtotime($model->password_reset_on) ? $model->password_reset_on : null,
                    'format'=>['datetime', $m->datetimeFormat], 
                    'valueColOptions' => ['style' => 'width: 30%'],
                    'displayOnly' => true
                ],
                [
                    'label' => Yii::t('user', 'Password Actions'),
                    'format' => 'raw',
                    'displayOnly' => true,
                    'value' => $passActions,
                    'valueColOptions' => ['style' => 'width:30%']
                ],
            ]
        ],
        [
            'group'=>true,
            'label'=> $m->icon('lock') . ' ' . Yii::t('user', 'Hidden Information'),
            'rowOptions'=>['class'=>'info']
        ],
        $getKey('password_hash', false),
        [
            'columns' => [
                $getKey('auth_key'),
                $getKey('email_change_key'),
            ]
        ],
        [
            'columns' => [
                $getKey('reset_key'),
                $getKey('activation_key'),
            ]
        ]
    ],
]) ?>
