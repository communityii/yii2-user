#Setup RBAC#

By default the yii2-user implements access control using `\yii\filters\AccessControl`.

In order to setup RBAC you need to change the **access** behavior for the controllers in your config file.

```php
    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
            'defaultControllerBehaviors' => [
                'access' => [
                    'class' => \common\components\AccessControl::className(),
                ]
            ],
        ],
    ],
```
