<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\controllers;

use Yii;
use yii\filters\AccessControl;
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
    const SETUP = 'begin_setup';

    /**
     * @inheritdoc
     */
    public $layout = Module::LAYOUT;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
        /**
         * @var Module $m
         * @var User   $user
         */
        $m = $this->_module;
        $userClass = $this->fetchModel(Module::MODEL_USER);
        if (isset($m->installAccessCode) && !$m->hasSuperUser()) {
            $model = new InstallForm(['scenario' => Module::SCN_ACCESS]);
            $session = Yii::$app->session;
            if (!isset($model->action)) {
                $model->action = self::SETUP;
            }
            if ($model->load(Yii::$app->request->post())) {
                if ($model->action === self::SETUP && $model->validate()) {
                    $model = new InstallForm(['scenario' => Module::SCN_INSTALL]);
                } elseif ($model->action === Module::SCN_ACCESS && $model->validate()) {
                    $model = new InstallForm(['scenario' => Module::SCN_INSTALL]);
                    $model->scenario = Module::SCN_INSTALL;
                    $model->action = Module::SCN_INSTALL;
                    if (isset(Yii::$app->params['adminEmail'])) {
                        $model->email = Yii::$app->params['adminEmail'];
                    }
                } elseif ($model->action === Module::SCN_INSTALL) {
                    $model->access_code = $m->installAccessCode;
                    if ($model->validate()) {
                        $user = new $userClass([
                            'username' => $model->username,
                            'password' => $model->password,
                            'email' => $model->email,
                            'status' => Module::STATUS_SUPERUSER,
                            'scenario' => Module::SCN_INSTALL
                        ]);
                        $user->setPassword($model->password);
                        $user->generateAuthKey();
                        if (!$user->save()) {
                            $session->setFlash('error', Yii::t(
                                'user',
                                'Error creating the superuser. Fix the following errors:<br>{errors}',
                                ['errors' => Module::showErrors($user)]
                            ));
                            $model->action = Module::SCN_INSTALL;
                            $model->scenario = Module::SCN_INSTALL;
                        } else {
                            $session->setFlash('success', Yii::t(
                                'user',
                                'User module successfully installed! You have been automatically logged in as the superuser (username: <b>{username}</b>).',
                                ['username' => $model->username]
                            ));
                            $session->setFlash('warning', Yii::t(
                                'user',
                                'You should now remove the <code>installAccessCode</code> setting from user module configuration for better security.'
                            ));
                            Yii::$app->user->login($user);
                            $user->setLastLogin();
                            return $this->forward(Module::ACTION_ADMIN_VIEW, ['id' => $user->id]);
                        }
                    } else {
                        $model->action = Module::SCN_ACCESS;
                        $model->scenario = Module::SCN_ACCESS;
                    }
                }
            }
            return $this->render($model->scenario, ['model' => $model, 'user' => isset($user) ? $user : null]);
        }
        return $this->safeRedirect();
    }
}