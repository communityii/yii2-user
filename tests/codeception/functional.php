<?php

$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME']     = YII_TEST_ENTRY_URL;

/*
 * Application configuration for functional tests
 */

$mainDirectory = __DIR__ . '/../../../..';

return yii\helpers\ArrayHelper::merge(
    require($mainDirectory . '/common/config/main.php'),
    require($mainDirectory . '/common/config/main-local.php'),
    require($mainDirectory . '/frontend/config/main.php'),
    require($mainDirectory . '/frontend/config/main-local.php'),
    [
        // ... tests only configs here
    ]
);