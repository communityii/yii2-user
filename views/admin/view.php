<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\detail\DetailView;
use kartik\ipinfo\IpInfo;
use comyii\user\Module;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\User $model
 */

$m = Yii::$app->getModule('user');
$this->title =  $m->message('user-details-title') . ': ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->username;
?>
<div class="user-view">
    <?= DetailView::widget([
        'model' => $model,
        'striped' => false,
        'hover' => true,
        'panel' => [
            'type' => 'primary',
            'heading' => '<i class="glyphicon glyphicon-user"></i> ' . $this->title,
        ],
        'attributes' => [
            [
                'group'=>true,
                'label'=> '<i class="glyphicon glyphicon-tag"></i> ' . $m->message('user-id-info-title'),
                'rowOptions'=>['class'=>'info']
            ],
            [
                'columns' => [
                    [
                        'attribute'=> 'id',
                        'format' => 'raw',
                        'value' => '<code><big>' . $model->id . '</big></code>',
                        'displayOnly' => true,
                        'valueColOptions' => ['style' => 'width: 30%']
                    ], 
                    [
                        'attribute' => 'username',
                        'valueColOptions' => ['style' => 'width: 30%']
                    ],
                ]
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'email',
                        'format' => 'email',
                        'valueColOptions' => ['style' => 'width: 30%']
                    ],    
                    [
                        'attribute'=> 'status', 
                        'format' => 'html',
                        'value'=>$model->statusHtml, 
                        'type' => DetailView::INPUT_SELECT2,
                        'valueColOptions' => ['style' => 'width: 30%'],
                        'widgetOptions'=>[
                            'data' => $model->getStatusList(),
                            'pluginOptions' => ['width' => '100%']
                        ]
                    ],
                ],
            ],
            [
                'group'=>true,
                'label'=> '<i class="glyphicon glyphicon-time"></i> ' . $m->message('user-log-info-title'),
                'rowOptions'=>['class'=>'info'],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'last_login_ip', 
                        'format' => 'raw',
                        'value' => '<kbd>' . $model->last_login_ip . '</kbd> ' . IpInfo::widget(['ip'=>$model->last_login_ip]),
                        'valueColOptions' => ['style' => 'width: 30%'], 
                        'displayOnly' => true
                    ],
                    ['attribute'=> 'created_on', 'valueColOptions' => ['style' => 'width: 30%'], 'displayOnly' => true],
                ]
            ],
            [
                'columns' => [
                    ['attribute'=> 'last_login_on', 'valueColOptions' => ['style' => 'width: 30%'], 'displayOnly' => true],
                    ['attribute'=> 'updated_on', 'valueColOptions' => ['style' => 'width: 30%'], 'displayOnly' => true],
                ]
            ],
            [
                'columns' => [
                    [
                        'attribute'=> 'password_reset_on', 
                        'valueColOptions' => ['style' => 'width: 30%'],
                        'displayOnly' => true
                    ],
                    [
                        'label' => $m->message('label-password-actions'),
                        'format' => 'raw',
                        'displayOnly' => true,
                        'value' => $m->actionButton(Module::ACTION_CHANGE, 'label-change-password', 'lock') . ' ' .
                            $m->actionButton(Module::ACTION_RESET, 'label-reset-password', 'refresh'),
                        'valueColOptions' => ['style' => 'width:30%']
                    ],
                ]
            ],
            [
                'group'=>true,
                'label'=> '<i class="glyphicon glyphicon-lock"></i> ' . $m->message('user-hidden-info-title'),
                'rowOptions'=>['class'=>'info']
            ],
            ['attribute'=> 'password_hash', 'displayOnly' => true],
            ['attribute'=> 'auth_key', 'displayOnly' => true],
            ['attribute'=> 'activation_key', 'displayOnly' => true],
            ['attribute'=> 'reset_key', 'displayOnly' => true],
        ],
    ]) ?>

</div>
