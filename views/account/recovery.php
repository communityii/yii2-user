<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use comyii\user\widgets\RecoveryForm;
use comyii\user\widgets\Logo;

$m = $this->context->module;
$this->title = Yii::t('user', 'Password Recovery');
/**
 * @var yii\web\View $this
 * @var comyii\user\models\Login $model
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