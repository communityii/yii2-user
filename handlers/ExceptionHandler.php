<?php

namespace comyii\user\handlers;

use Yii;
use yii\base\Object;
use comyii\user\Module;
use comyii\user\events\ExceptionEvent;

class ExceptionHandler extends Object
{
    /**
     * Send an exception mail
     *
     * @param ExceptionEvent $event
     *
     * @return bool
     */
    public static function sendMail($event)
    {
        /**
         * @var Module $m
         * @var \yii\swiftmailer\Mailer $mailer
         */
        $m = Yii::$app->getModule('user');
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = $m->notificationSettings['viewPath'];
        $params = Yii::$app->params;
        return $mailer
            ->compose('exception', ['exception' => $event->exception, 'event' => $event->event])
            ->setFrom([$params['adminEmail'] => $params['mailFrom']])
            ->setTo($params['adminEmail'])
            ->setSubject('Site Error: Exception Occured')
            ->send();
    }
}
