<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\controllers;

use Yii;
use communityii\user\Module;
use communityii\user\models\User;
use communityii\user\models\InstallForm;

/**
 * Install controller for managing the install and setup of admin user for this module.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class InstallController extends BaseController
{
    public function actionIndex()
    {
        if (isset($this->module->installAccessCode) && !$this->module->isSuperUserSet()) {
            $model = new InstallForm(['scenario' => Module::UI_ACCESS]);
            if ($model->load(Yii::$app->request->post())) {
                $model->scenario = $model->action;
                if ($model->action === Module::UI_ACCESS && $model->validate()) {
                    $model = new InstallForm(['scenario' => Module::UI_INSTALL]);
                }
                elseif ($model->action === Module::UI_INSTALL && $model->validate()) {
                    $user = User::create([
                        'username' => $model->username,
                        'password' => $model->password,
                        'email' => $model->email,
                        'status' => User::STATUS_SUPERUSER
                    ]);
                    if ($user === null) {
                        Yii::$app->session->setFlash('error', Yii::t('user', 'Error creating the superuser. Ensure you have the right database permissions.'));
                    }
                    else {
                        Yii::$app->session->setFlash('success', Yii::t('user', 'The user module has been successfully installed. You have logged in as <strong>{username}</strong> and you are the superuser.',
                            ['username' => $model->username]));
                        Yii::$app->login($user);
                        return $this->redirect('admin/index');
                    }
                }
            }
            return $this->render($model->scenario, ['model' => $model]);
        }
        return $this->safeRedirect();
    }
}