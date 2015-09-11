<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use comyii\user\widgets\RegistrationForm;
use comyii\user\widgets\Logo;

/**
 * @var yii\web\View $this
 */
?>
<div class="text-center">
    <?= Logo::widget() ?>
</div>
<div class="y2u-box">
    <?= RegistrationForm::widget([
        'model' => $model,
        'title' => $registerTitle,
        'hasSocialAuth' => $hasSocialAuth,
        'authAction' => $authAction,
        'authTitle' => $authTitle
    ]); ?>
</div>
