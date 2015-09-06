<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\detail\DetailView;
use kartik\ipinfo\IpInfo;
use comyii\user\Module;
use comyii\user\models\User;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\User $user
 */

$m = $this->context->module;
$this->title =  Yii::t('user', 'User Profile') . ' (' . $user->username . ')';
$this->params['breadcrumbs'][] = $user->username;
?>
<?= DetailView::widget([
    'model' => $user,
    'striped' => false,
    'hover' => true,
    'buttons1' => "{update}",
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
                    'attribute' => 'username',
                    'valueColOptions' => ['style' => 'width: 30%']
                ],
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'valueColOptions' => ['style' => 'width: 30%']
                ],    
            ]
        ],
    ]
]) ?>
