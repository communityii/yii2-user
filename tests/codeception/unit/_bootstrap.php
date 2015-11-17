<?php
//'user' => [
//            'class' => 'comyii\user\Module',
////            'installAccessCode' => '1234',
//            // setup your module preferences
//            // refer all module settings in the docs
//            'registrationSettings' => [
//                'autoActivate' => false,
//                'captcha' => false,
//                'types' => [
//                    'patient' => '\common\models\PatientUser',
//                    'dispensary' => '\common\models\DispensaryUser'
//                ],
//                'randomUsernames' => true,
//                'randomPasswords' => true,
//            ],
//            'socialSettings' => [
//                'enabled' => true,
//            ],
//            'profileSettings' => [
//                'enabled' => false,
//                'baseUrl' => '',
//                'defaultAvatar' => 'images/avatar.png',
//            ],
//            'defaultControllerBehaviors' => [
////                'access' => [
////                    'class' => \mdm\admin\components\AccessControl::className(),
////                    'allowActions' => [
////                        'user/register',
////                    ]
////                ]
//            ],
//            'modelSettings' => [
//                comyii\user\Module::MODEL_USER => '\common\models\User',
//                comyii\user\Module::MODEL_PROFILE => '\common\models\UserProfile'
//            ],
//            'buttons' => [
//                comyii\user\Module::BTN_RESET_FORM => null,
//            ],
//            'userTypes' => [
//                \common\models\User::TYPE_PATIENT => [
//                    'class' => '\common\models\PatientUser',
//                    'key' => 'patient',
//                    'modelSettings' => [
//                        comyii\user\Module::MODEL_USER => '\common\models\PatientUser',
//                        comyii\user\Module::MODEL_PROFILE => '\common\models\PatientUserProfile'
//                    ],
//                    'layoutSettings' => [
//                        comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/patient',
//                        comyii\user\Module::VIEW_PROFILE_UPDATE => '@frontend/views/layouts/patient',
//                        comyii\user\Module::VIEW_PASSWORD => '@frontend/views/layouts/patient',
//                    ],
////                    'viewSettings' => [
////                        comyii\user\Module::VIEW_PROFILE_INDEX => '@backend/modules/patient/views/account/profile'
////                    ],
//                ],
//                \common\models\User::TYPE_DISPENSARY =>  [
//                    'key' => 'dispensary',
//                    'layoutSettings' => [
//                        comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/dispensary',
//                        comyii\user\Module::VIEW_PROFILE_UPDATE => '@frontend/views/layouts/dispensary',
//                        comyii\user\Module::VIEW_PASSWORD => '@frontend/views/layouts/dispensary',
//                    ],
//                    'viewSettings' => [
//                        comyii\user\Module::VIEW_PROFILE_INDEX => '@backend/modules/dispensary/views/account/profile'
//                    ],
//                ],
//            ],
//            'layoutSettings' => [
//                \common\models\User::TYPE_DISPENSARY => [
//                    comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/dispensary',
//                    comyii\user\Module::VIEW_PROFILE_UPDATE => '@frontend/views/layouts/dispensary',
//                    comyii\user\Module::VIEW_PASSWORD => '@frontend/views/layouts/dispensary',
//                ],
//                \common\models\User::TYPE_PATIENT => [
//                    comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/patient',
//                    comyii\user\Module::VIEW_PROFILE_UPDATE => '@frontend/views/layouts/patient',
//                    comyii\user\Module::VIEW_PASSWORD => '@frontend/views/layouts/patient',
//                ]
//            ],
//            'viewSettings' => [
//                \common\models\User::TYPE_DISPENSARY => [
//                    comyii\user\Module::VIEW_PROFILE_INDEX => '@backend/modules/dispensary/views/account/profile'
//                ]
//            ],
//            'statusClasses' => [
//                1 => 'test1',
//                10 => 'test10'
//            ],
//            'now' => function () { return date('Y-m-d H:i:s'); },
//            'on beforeRegister'=>['\common\events\RegistrationHandler','beforeRegister'],
//            'on registerComplete'=>['\common\events\RegistrationHandler','registerComplete']
//        ],
//use AspectMock\Kernel;
//
////$config = require(__DIR__.'/config/web.php');
//
$mainDirectory = __DIR__ . '/../../../../../..';


$config = yii\helpers\ArrayHelper::merge(
    require($mainDirectory . '/common/config/main.php'),
    require($mainDirectory . '/common/config/main-test.php'),
    require($mainDirectory . '/frontend/config/main.php'),
    require($mainDirectory . '/frontend/config/main-test.php')
);

(new yii\web\Application($config));
