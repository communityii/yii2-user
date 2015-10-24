
## User Types

To add custom user types extend the user class and add the user types as constants and then add them to the config.
For example if you want two user types, one for customers, and another for vendors...

```php
namespace common\models;
class User extends \comyii\user\models\User
{
    const TYPE_CUSTOMER = 1;
    const TYPE_VENDOR = 2;
}
```
Then update your config.
```php
    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
            'userTypes'=>[
                common\models\User::TYPE_CUSTOMER => 'customer',
                common\models\User::TYPE_VENDOR => 'vendor'
            ],
        ],
    ],
```

###Custom Layouts for User Types

```php
    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
            'userTypes'=>[
                common\models\User::TYPE_CUSTOMER => 'customer',
                common\models\User::TYPE_VENDOR => 'vendor'
            ],
            'layoutSettings'=>[
                common\models\User::TYPE_CUSTOMER => [
                    comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/customer',
                    comyii\user\Module::VIEW_PROFILE_UPDATE => '@frontend/views/layouts/customer',
                    comyii\user\Module::VIEW_PASSWORD => '@frontend/views/layouts/customer',
                ],
                common\models\User::TYPE_VENDOR => [
                    comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/vendor',
                    comyii\user\Module::VIEW_PROFILE_UPDATE => '@frontend/views/layouts/vendor',
                    comyii\user\Module::VIEW_PASSWORD => '@frontend/views/layouts/vendor',
                ],
            ],
        ],
    ],
```

###Custom Views for User Types

```php
    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
            'userTypes'=>[
                common\models\User::TYPE_CUSTOMER => 'customer',
                common\models\User::TYPE_VENDOR => 'vendor'
            ],
            'viewSettings' => [
                common\models\User::TYPE_VENDOR => [
                    comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/vendor/profile'
                ]
            ],
        ],
    ],
```


##User Type Routes
Any custom user types defined in the config will have a registration page using the following url rule:

```
'register/<type:>' => 'account/register',
```

Which of course can also be customized by updating the 'urlRules' in the module config.

##Examples

[Registration Event and Custom User Types](register-custom-user-type.md)
