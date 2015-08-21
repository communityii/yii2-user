<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model comyii\user\models\UserBanLog */

$this->title = Yii::t('user', 'Update {modelClass}: ', [
    'modelClass' => 'User Ban Log',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'User Ban Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Update');
?>
<div class="user-ban-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
