<?php
use kartik\select2\Select2;
use yii\widgets\MaskedInput;
?>
<?= $form->field($profile, 'first_name')->textInput(['maxlength' => true]) ?>
<?= $form->field($profile, 'last_name')->textInput(['maxlength' => true]) ?>
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