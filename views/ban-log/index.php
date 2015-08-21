<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel comyii\user\models\UserBanLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'User Ban Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-ban-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('user', 'Create User Ban Log'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_ip',
            'ban_reason',
            'revoke_reason',
            'user_id',
            // 'banned_till',
            // 'created_on',
            // 'updated_on',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
