<?php

namespace comyii\user\commands;
 
use yii\console\Controller;
use yii\helpers\Console;
 
class DefaultController extends Controller
{
    /**
     * Hello world test
     * @param string $message the message
     */
    public function actionIndex($message = 'hello world from module')
    {
        echo $message . "\n";
    }
}