<?php

use AspectMock\Kernel;

//$config = require(__DIR__.'/config/web.php');

$mainDirectory = __DIR__ . '/../../../../..';

require($mainDirectory . '/vendor/autoload.php');
$kernel = Kernel::getInstance();
$kernel->init([
    'debug'        => true,
    'includePaths' => [__DIR__.'/../../', $mainDirectory . '/vendor/'],
    'excludePaths' => [__DIR__],
    'cacheDir'     => '/tmp/yii2-user/aop',
]);
$kernel->loadFile($mainDirectory.'/vendor/yiisoft/yii2/Yii.php');
require($mainDirectory . '/common/config/bootstrap.php');
require($mainDirectory . '/frontend/config/bootstrap.php');


defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV_DEV', true);
//$config['components']['assetManager']['forceCopy'] = true;

