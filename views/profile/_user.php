<?php
$editSettings = $module->getEditSettingsUser($model);
if ($editSettings['changeUsername']) {
    echo $form->field($model, 'username')->textInput(['maxlength' => true]);
} else {
    echo $form->field($model, 'username')->staticInput();
}
if ($editSettings['changeEmail']) {
    echo $form->field($model, 'email')->textInput(['maxlength' => true]);
    if ($model->email_new != null) {
        echo $form->field($model, 'email_new')->staticInput();
    }
} else {
    echo $form->field($model, 'email')->staticInput();
}
?>