<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\User $model
 */

$m = Yii::$app->getModule('user');
$this->title =  $m->message('user-details-title') . ' &raquo; ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->username;
?>
<div class="user-view">
    <?= DetailView::widget([
        'model' => $model,
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
            ['attribute'=> 'id', 'displayOnly' => true], 
            [
                'attribute' => 'username',
                'inputContainer' => ['class'=>'col-sm-6'],
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'inputContainer' => ['class'=>'col-sm-6'],
            ],    
            [
                'attribute'=> 'status', 
                'format' => 'html',
                'value'=>$model->statusHtml, 
                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions'=>[
                    'data' => $model->getStatusList(),
                    'options' => ['placeholder' => 'Select ...'],
                    'pluginOptions' => ['allowClear' => true, 'width' => '100%']
                ],
                'inputContainer' => ['class'=>'col-sm-6'],
            ],
            [
                'group'=>true,
                'label'=> '<i class="glyphicon glyphicon-time"></i> ' . $m->message('user-log-info-title'),
                'rowOptions'=>['class'=>'info'],
            ],
            ['attribute'=> 'last_login_ip', 'displayOnly' => true],
            ['attribute'=> 'last_login_on', 'displayOnly' => true],              
            ['attribute'=> 'password_reset_on', 'displayOnly' => true],              
            ['attribute'=> 'created_on', 'displayOnly' => true],              
            ['attribute'=> 'updated_on', 'displayOnly' => true],
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
