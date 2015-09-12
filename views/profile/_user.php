<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

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