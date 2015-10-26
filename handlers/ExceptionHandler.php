<?php

namespace comyii\user\handlers;


class ExceptionHandler extends \yii\base\Object
{
    /**
     * @param \comyii\events\ExceptionEvent $event
     */
    public static function mail($event)
    {
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = $this->notificationSettings['viewPath'];
        return $mailer
            ->compose('exception', ['exception' => $event->ex, 'event' => $event->event])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['mailFrom']])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('Site Error: Exception Occured')
            ->send();
    }
}