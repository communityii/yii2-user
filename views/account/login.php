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
 * @var LoginForm $model
 * @var string $loginTitle
 * @var string $authTitle
 * @var bool $hasSocialAuth
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
        'authTitle' => $authTitle
    ]); ?>
</div>