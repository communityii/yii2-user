#Setup RBAC#

By default yii2-user implements basic access control using `\yii\filters\AccessControl`.

In order to implement RBAC you need to update the `access` behavior in the controllers.

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



