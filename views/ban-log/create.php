<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model comyii\user\models\UserBanLog */

$this->title = Yii::t('user', 'Create User Ban Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'User Ban Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-ban-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
