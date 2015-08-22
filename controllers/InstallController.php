<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\controllers;

use Yii;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\models\InstallForm;

/**
 * Install controller for managing the install and setup of admin user for this module.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class InstallController extends BaseController
{
    const UI_INIT_SETUP = 'begin_setup';
    public $layout = 'install';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $m = $this->module;
        if (isset($m->installAccessCode) && !$m->hasSuperUser()) {
            $model = new InstallForm(['scenario' => Module::UI_ACCESS]);
            if (!isset($model->action)) {
                $model->action = self::UI_INIT_SETUP;
            }
            if ($model->load(Yii::$app->request->post())) {
                if ($model->action === self::UI_INIT_SETUP && $model->validate()) {
                    $model = new InstallForm(['scenario' => Module::UI_INSTALL]);
                }
                elseif ($model->action === Module::UI_ACCESS && $model->validate()) {
                    $model = new InstallForm(['scenario' => Module::UI_INSTALL]);
                    $model->scenario = Module::UI_INSTALL;
                    $model->action = Module::UI_INSTALL;
                    if (isset(Yii::$app->params['adminEmail'])) {
                        $model->email = Yii::$app->params['adminEmail'];
                    }
                }
                elseif ($model->action === Module::UI_INSTALL) {
                    $model->access_code = $m->installAccessCode;
                    if ($model->validate()) {
                        $user = new User([
                            'username' => $model->username,
                            'password_raw' => $model->password,
                            'email' => $model->email,
                            'status' => User::STATUS_SUPERUSER,
                            'scenario' => Module::UI_INSTALL
                        ]);
                        $user->setPassword($model->password);
                        $user->generateAuthKey();
                        $user->generateResetKey();                        
                        if (!$user->save()) {
                            $m->setFlash('error', 'install-error', ['errors' => Module::showErrors($user)]);
                            $model->action = Module::UI_INSTALL;
                            $model->scenario = Module::UI_INSTALL;
                        }
                        else {
                            $m->setFlash('success', 'install-success',  ['username' => $model->username]);
                            $m->setFlash('warning', 'install-warning');
                            Yii::$app->user->login($user);
                            $user->setLastLogin();
                            return $this->forward(Module::ACTION_ADMIN_VIEW, ['id'=>$user->id]);
                        }
                    }
                    else {
                        $model->action = Module::UI_ACCESS;
                        $model->scenario = Module::UI_ACCESS;
                    }
                }
            }
            return $this->render($model->scenario, ['model' => $model, 'user' => isset($user) ? $user : null]);
        }
        return $this->safeRedirect();
    }
}