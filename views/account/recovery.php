<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\web\View;
use comyii\user\widgets\RecoveryForm;
use comyii\user\widgets\Logo;
use comyii\user\models\LoginForm;

$m = Yii::$app->getModule('user');
$this->title = Yii::t('user', 'Password Recovery');

/**
 * @var View      $this
 * @var LoginForm $model
 */
?>
<div class="text-center">
    <?= Logo::widget() ?>
</div>
<div class="y2u-box">
    <?= RecoveryForm::widget([
        'model' => $model,
        'title' => $this->title
    ]); ?>
</div>