<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use kartik\select2\Select2;
use yii\widgets\MaskedInput;

/**
 * @var comyii\user\models\UserProfile $profile
 */

echo $form->field($profile, 'first_name')->textInput(['maxlength' => true]);
echo $form->field($profile, 'last_name')->textInput(['maxlength' => true]);
?>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($profile, 'gender')->widget(Select2::classname(), ['data' => $profile->genderList]) ?>
    </div>
    <div class="col-md-8">
        <?= $form->field($profile, 'birth_date')->widget(MaskedInput::classname(), [
            'clientOptions' => ['alias' => 'yyyy-mm-dd']
        ]) ?>
    </div>
</div>