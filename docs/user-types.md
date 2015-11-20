User Types
===========

- [User Types Setup](#user-types-setup)
- [User Type Custom Layouts](#user-type-custom-layouts)
- [User Type Custom Views](#user-type-custom-views)
- [User Type Routes](#user-type-routes)
- [Examples](#examples)

---

[:back: guide](index.md#key-concepts)

User types allow you to control your application layout and view settings for different types of users. To add/configure the user types for your application, extend the `comyii\user\models\User` class. Then define your user types as constants and then add them to the module configuration.

For example, let's consider if we want two user types, one for **customers**, and another for **vendors**. 

## User Types Setup

**Step 1:** Extend the `User` model as shown below:

```php
namespace common\models;
class User extends \comyii\user\models\User
{
    const TYPE_CUSTOMER = 1;
    const TYPE_VENDOR = 2;
}
```

**Step 2:** Then update your module configuration for the user types:

```php

    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
            'types'=>[
                common\models\User::TYPE_CUSTOMER => [
                    'tag' => 'customer',
                ],
                common\models\User::TYPE_VENDOR => [
                    'tag' => 'vendor',
                ],
            ],
        ],
    ],
```

[:back: top](#user-types) | [:back: guide](index.md#key-concepts)

## User Type Custom Layouts

```php
    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
            'types'=>[
                common\models\User::TYPE_CUSTOMER => [
                    'tag' => 'customer',
                    'layouts' => [
                        'layouts' => [
                            comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/customer',
                            comyii\user\Module::VIEW_LOGIN => '@frontend/views/layouts/customer',
                        ]
                    ],
                ],
                common\models\User::TYPE_VENDOR => [
                    'tag' => 'vendor',
                    'layouts' => [
                        'layouts' => [
                            comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/vendor',
                            comyii\user\Module::VIEW_LOGIN => '@frontend/views/layouts/vendor',
                        ]
                    ],
                ],
            ],
        ],
    ],
```

[:back: top](#user-types) | [:back: guide](index.md#key-concepts)

## User Type Custom Views

```php
    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
            'types'=>[
                common\models\User::TYPE_CUSTOMER => [
                    'tag' => 'customer',
                    'layouts' => [
                        'layouts' => [
                            comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/customer',
                            comyii\user\Module::VIEW_LOGIN => '@frontend/views/layouts/customer',
                        ]
                    ],
                ],
                common\models\User::TYPE_VENDOR => [
                    'tag' => 'vendor',
                    'views' => [
                        'views' => [
                            comyii\user\Module::VIEW_PROFILE_INDEX => '@frontend/views/layouts/vendor',
                            comyii\user\Module::VIEW_LOGIN => '@frontend/views/layouts/vendor',
                        ]
                    ],
                ],
            ],
        ],
    ],
```

[:back: top](#user-types) | [:back: guide](index.md#key-concepts)

## User Type Routes
Any custom user types defined in the config will have a registration page using the following url rule:

```
'register/<type:>' => 'account/register',
```

Note that these can also be customized by updating the `urlRules` in the module configuration.

[:back: top](#user-types) | [:back: guide](index.md#key-concepts)

## Examples

- [Register Custom User Type](register-custom-user-type.md)

[:back: top](#user-types) | [:back: guide](index.md#key-concepts)

