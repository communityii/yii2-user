<?php

namespace comyii\user\commands;
 
use yii\console\Controller;
 
class DefaultController extends Controller
{
    const GREET = 'Hello World! Welcome to the "communityii/yii2-user" module.';

    /**
     * Hello world test
     * @param string $message the message
     */
    public function actionIndex($message = self::GREET)
    {
        echo $message . "\n";
    }
}