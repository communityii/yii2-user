Setup RBAC
==========

[:back: guide](index.md#advanced-customization)

By default `yii2-user` implements basic access control using `\yii\filters\AccessControl`. In order to implement Role Based Access Control (RBAC), you need to update the `access` behavior in the controllers. For example:

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

You may read the [Yii 2 RBAC Documentation](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#rbac) for details on configuring RBAC for your application. 

[:back: top](#setup-rbac) | [:back: guide](index.md#advanced-customization)
