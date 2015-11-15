<?php

namespace comyii\user\handlers;

use Yii;
use yii\base\Object;
use yii\base\ActionEvent;
use comyii\user\components\User;

class RbacHandler extends Object
{
    /**
     * Assign user type as role
     *
     * @param \comyii\user\events\account\RegistrationEvent $event
     */
    public static function assignRole($event)
    {
        $authManager = Yii::$app->authManager;
        /**
         * @var \comyii\user\models\User $model
         */
        $model = $event->model;
        $authManager->assign($authManager->createRole($event->type), $model->getId());
    }

    /**
     * Before module action
     *
     * @param ActionEvent $event
     */
    public static function beforeAction($event)
    {
        /**
         * @var User $user
         */
        $user = Yii::$app->user;
        $queryParams = Yii::$app->request->queryParams;
        if (isset($queryParams['id']) && $queryParams['id'] != $user->identity->id) {
            $user->can('user-view-all');
        }
        switch ($event->action->controller->id) {
            case 'account':
                switch ($event->action->id) {
                    case '':
                        // todo
                }
                break;
        }
    }
}
