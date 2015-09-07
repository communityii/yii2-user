<?php
$editSettings = $module->getEditSettingsUser($model);
if ($editSettings['changeUsername']) {
    echo $form->field($model, 'username')->textInput(['maxlength' => true]);
}
if ($editSettings['changeEmail']) {
    echo $form->field($model, 'email')->textInput(['maxlength' => true]);
}