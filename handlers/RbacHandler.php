<?php

namespace comyii\user\handlers;


class RbacHandler extends \yii\base\Object
{
    
    public static function init($event)
    {
        
    }
    /**
     * Assign user type as role
     * @param \comyii\events\RegisterEvent $event
     */
    public static function assignRole($event)
    {
        Yii::$app->authManager->assign(Yii::$app->authManager->createRole($event->type), $event->model->getId());
    }
    
    /**
     * Before module action
     * @param \yii\base\ActionEvent $event
     */
    public static function beforeAction($event)
    {
        if (isset(Yii::$app->request->queryParams['id']) && Yii::$app->request->queryParams['id'] != Yii::$app->user->identity->id) {
            Yii::$app->user->can('user-view-all');
        }
        switch ($event->action->controller->id)
        {
            case 'account':
                switch ($event->action->id)
                {
                    case '':
                }
                break;
        }
    }
    
}