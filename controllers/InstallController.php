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
 * Install controller for managing the install and setup of the superuser for this module.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class InstallController extends BaseController
{
    const UI_INIT_SETUP = 'begin_setup';
    public $layout = 'install';

    /**
     * @inheritdoc
     */
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

    /**
     * Module installation action
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $m = $this->module;
        $userClass = $this->getConfig('modelSettings', Module::MODEL_USER);
        if (isset($m->installAccessCode) && !$m->hasSuperUser()) {
            $model = new InstallForm(['scenario' => Module::UI_ACCESS]);
            $session = Yii::$app->session;
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
                        $user = new $userClass([
                            'username' => $model->username,
                            'password' => $model->password,
                            'email' => $model->email,
                            'status' => User::STATUS_SUPERUSER,
                            'scenario' => Module::UI_INSTALL
                        ]);
                        $user->setPassword($model->password);
                        $user->generateAuthKey();
                        $user->generateResetKey();                        
                        if (!$user->save()) {
                            $session->setFlash('error', Yii::t(
                                'user', 
                                'Error creating the superuser. Fix the following errors:<br>{errors}', 
                                ['errors' => Module::showErrors($user)]
                            ));
                            $model->action = Module::UI_INSTALL;
                            $model->scenario = Module::UI_INSTALL;
                        }
                        else {
                            $session->setFlash('success', Yii::t(
                                'user', 
                                'User module successfully installed! You have been automatically logged in as the superuser (username: <b>{username}</b>).',  ['username' => $model->username]
                            ));
                            $session->setFlash('warning', Yii::t(
                                'user', 
                                'You should now remove the <code>installAccessCode</code> setting from user module configuration for better security.'
                            ));
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