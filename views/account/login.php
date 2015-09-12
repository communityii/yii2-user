<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use comyii\user\widgets\LoginForm;
use comyii\user\widgets\Logo;
/**
 * @var yii\web\View $this
 * @var comyii\user\models\Login $model
 */
?>
<div class="text-center">
    <?= Logo::widget() ?>
</div>
<div class="y2u-box">
    <?= LoginForm::widget([
        'model' => $model,
        'title' => $loginTitle,
        'hasSocialAuth' => $hasSocialAuth,
        'authAction' => $authAction,
        'authTitle' => $authTitle
    ]); ?>
</div>