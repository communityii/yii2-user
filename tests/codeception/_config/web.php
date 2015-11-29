<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

use comyii\user\components\Buttons;
use comyii\user\components\Views;
use comyii\user\tests\codeception\_app\models\TestUser;

return [
    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
//            'installAccessCode' => '1234',
            // setup your module preferences
            // refer all module settings in the docs
            'components' => [
                'layouts' => [
                    'class' => 'comyii\user\components\Layouts',
                    'layouts' => [
                        Views::VIEW_PROFILE_INDEX => '@frontend/views/layouts/patient',
                        Views::VIEW_LOGIN => 'custom',
                    ]
                ],
                'views' => [
                    'class' => 'comyii\user\components\Views',
                    'views' => [
                    ]
                ],
                'actions' => [
                    'class' => 'comyii\user\components\Actions',
                    'actions' => [
                        
                    ]
                ],
                'buttons' => [
                    'buttons' => [
                        Buttons::BTN_RESET_FORM => null,
                    ],
                ]
            ],
            'types' => [
                TestUser::TYPE_VENDOR => [
                    'route' => 'vendor',
                    'layouts' => [
                        'layouts' => [
                            Views::VIEW_PROFILE_INDEX => '@frontend/views/layouts/vendor',
                        ]
                    ],
                ],
                TestUser::TYPE_CUSTOMER => [
                    'route' => 'customer',
                    'layouts' => [
                        'layouts' => [
                            Views::VIEW_PROFILE_INDEX => '@frontend/views/layouts/customer',
                        ]
                    ],
                ],
            ],
        ],
    ],
];