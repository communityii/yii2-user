<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel comyii\user\models\UserProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'User Profiles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('user', 'Create User Profile'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'display_name',
            'first_name',
            'last_name',
            'avatar_url:ntext',
            // 'created_on',
            // 'updated_on',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
