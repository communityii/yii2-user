<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use comyii\user\widgets\RegistrationForm;
use comyii\user\widgets\Logo;
use comyii\user\models\LoginForm;

/**
 * @var LoginForm $model
 * @var string    $registerTitle
 * @var string    $authTitle
 * @var mixed     $authAction
 * @var bool      $hasSocialAuth
 * @var string    $type
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
        'authTitle' => $authTitle,
        'userType' => $type
    ]); ?>
</div>
