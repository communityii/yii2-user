yii2-user
=========

A configurable user management module for Yii framework 2.0 with inbuilt social authentication and various user management controls. The module has been intentionally kept simple - yet enable yii developers to achieve user management & control for various use cases and scenarios. The module does not have RBAC (Role Based Access Control) inbuilt as an intention to keep it simple. There are few access control methods that help achieve various use cases. However, for building advanced / complex specific use cases, almost every feature has been enabled to be configurable and extensible by the developer. Be it the module rules & settings, the model classes, the controller actions, the layouts, or the views - each of these can be customized. In addition few reusable components and widgets have been provided for expanding and building further use cases.

#### _This extension is under development and not completely ready for use._

## Features and Concepts

### Primary User Statuses

#### Superuser

_INTERNAL STATUS_

There is typically one superuser for each application and is created by default on install. Superusers cannot be modified by other users except the superuser himself / herself.

#### Admin

_EDITABLE STATUS_

Administrators behave like superusers - but can be inactivated or manipulated by superusers or administrators based on module level settings.

#### Pending

_INTERNAL STATUS_

By default users are placed in pending status when registered. This will not be applicable if you have `autoActivate` setting to `true` in your `registrationSettings`

#### Active

_EDITABLE STATUS_

Whether the user is active and allowed access to the application. Admin and Superuser have accesses to application by default.

#### Inactive

_EDITABLE STATUS_

Whether the user is inactive (banned / deleted). No access will be allowed for inactive users to the application. Admin and Superuser can view / manage inactive users.

### Secondary Statuses

#### Expired

_INTERNAL STATUS_

Whenever a password for an user account expires, the user is placed into this additional status, and guided to changing his / her password. This feature can be controlled by configuring `passwordExpiry` in `passwordSettings`. This status is additionally applicable in addition to the primary statuses above (other than `Inactive`).

#### Locked

_INTERNAL STATUS_

Whenever an user types a wrong password for a specified number of times consecutively - his / her account is locked. This is controlled by configuring `wrongAttempts` in  `passwordSettings`. This status is additionally applicable in addition to the primary statuses above (other than `Inactive`).

#### _More concepts and usage information to be added later in detailed documentation_

## Installation & Setup

### Installation

The preferred way to install this extension is through [composer](http: /  / getcomposer.org / download / ).

Either run

```
$ php composer.phar require communityii/yii2-user "@dev"
```

or add

```
"communityii/yii2-user": "@dev"
```

to the `require` section of your `composer.json` file.

Run `$ php composer.phar update` from your console to get the latest version of the module packages.

### User Module Setup
Setup the module in your configuration file as shown below. The module depends on and uses the [yii2-grid module](https://demos.krajee.com/grid), so you must also setup the `gridview` module also as shown below. You can set this within `common/config/main.php` if using with Yii 2 advanced app or within `config/web.php` for Yii 2 basic app.

```php
'modules' => [
    'user' => [
        'class' => 'comyii\user\Module',
        'installAccessCode' => '<YOUR_ACCESS_CODE>',
        // setup your module preferences
        // refer all module settings in the docs
        'registrationSettings' => [
            'autoActivate' => false
        ],
        'socialSettings' => [
            'enabled' => true
        ],
        'profileSettings' => [
            'enabled' => true,
            'baseUrl' => 'http://localhost/kvdemo/uploads'
        ]
    ],
    'gridview' => [
        'class' => 'kartik\grid\Module',
    ]
],
//  your other modules
```

### User Component Setup

Setup the user component in your configuration file for the yii2-user module.  You can set this within `common/config/main.php` if using with Yii 2 advanced app or within `config/web.php` for Yii 2 basic app.

```php
'components' => [
   //  user authentication component
    'user' => [
        'class' => 'comyii\user\components\User',
       //  other component settings
    ],
   //  your other components
],
```

### Database Connection Setup

Configure the database connection component `db` in your configuration file to reflect the right `tablePrefix` as needed for your environment.

```php
'components' => [
   //  database connection component
    'db' => [
        'class' => 'yii\db\Connection',
        //  your table prefix
        'tablePrefix' => 'tbl_',
        //  your connection settings
         'dsn' => 'mysql:host=localhost;dbname=[DB_NAME]',
         'username' => '[SCHEMA_USERNAME]',
         'password' => '[SCHEMA_PASSWORD]',
         'charset' => 'utf8',
    ],
   //  your other components
],
```

### Create User Database

Run the database migrations from your command console to setup the database schema for the module.

```
$ php yii migrate/up --migrationPath=@vendor/communityii/yii2-user/migrations
```

### Setup Mailer Component

The module uses [yii2-swiftmailer](https://github.com/yiisoft/yii2-swiftmailer) extension to generate emails to the users for various actions. You would need to configure the mailer component as shown below. For example, the configuration file would be `common\config\main-local.php` if you are using the Yii 2 advanced app. Note any `viewPath` set within this component will be ignored by the `yii2-user` module. Instead, the setting within `comyii\user\Module::notificationSettings['viewPath']` will be considered for parsing the mailer templates within this module.

```php
'components' => [
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@common/mail', // the
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        'useFileTransport' => true,
        /*
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'smtp.gmail.com',
            'username' => '<your-email@gmail.com>',
            'password' => '<your-password>',
            'port' => '587',
            'encryption' => 'tls',
        ],
        */
    ],
]
```

### Setup Social Component (_Optional_)

If you wish to enable users to authenticate via social connections (e.g. Facebook, Twitter etc.) - then you need to setup the auth client component. The module uses the [yii2-authclient](https://github.com/yiisoft/yii2-authclient) extension for social authentication. You would need to configure the `authClientCollection` component in your Yii configuration file (append the following to the `components` section and configure your necessary clients). 
 
```php
'authClientCollection' => [
    'class' => 'yii\authclient\Collection',
    'clients' => [
        'google' => [
            'class' => 'yii\authclient\clients\GoogleOpenId'
        ],
        'facebook' => [
            'class' => 'yii\authclient\clients\Facebook',
            'clientId' => 'facebook_client_id',
            'clientSecret' => 'facebook_client_secret',
        ],
        // etc.
    ],
]
```

### Getting Started

Login to the url pertaining to the user module path (e.g. `http://localhost/app/user`) and you should be automatically guided with an installation wizard. Finish the superuser setup to proceed using the module.

## Usage with Yii 2 Advanced Application Template

You can configure the module to work easily with the Yii2 Advanced Template and have different sessions for frontend and backend of the Yii2 advanced app. For this you may carry out the following steps:

### Setup Session Storage Folders

Create the following folders in your Yii 2 advanced app.

- `frontend/runtime/sessions`
- `backend/runtime/sessions`

### Configure Frontend Settings

Add the following configuration for cookies and sessions to your frontend app within `frontend/config/main-local.php`.

```php
'components' => [
    'request' => [
        // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
        'cookieValidationKey' => 'UNIQUE_KEY_FRONTEND',
    ],
    // unique identity cookie configuration for frontend
    'user' => [
        'identityCookie' => [
            'name' => '_frontendUser', // unique for frontend
            'path' => '/' // set it to correct path for frontend app.
        ]
    ],
    // unique session configuration for frontend
    'session' => [
        'name' => '_frontendSessionId', // unique for frontend
        'savePath' => __DIR__ . '/../runtime/sessions' // set it to correct path for frontend app.
    ]
],
```

### Configure Backend Settings

Add the following configuration for cookies and sessions to your backend app within `backend/config/main-local.php`. Note you can optionally configure a different URL manager in `backend` to get access to frontend URL routes (for example `urlManagerFE` as shown below).

```php
'components' => [
    'request' => [
        // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
        'cookieValidationKey' => 'UNIQUE_KEY_BACKEND',
        // unique CSRF cookie parameter for backend
        'csrfParam' => '_backendCsrf',
    ],
    // unique identity cookie parameter for backend
    'user' => [
        'identityCookie' => [
            'name' => '_backendCookie', // unique for backend
            'path' => '/backend', // set it to correct path for backend app
        ]
    ],
    // unique identity session parameter for backend
    'session' => [
        'name' => '_backendSessionId',
        'savePath' => __DIR__ . '/../runtime/sessions',  
    ],    
    // url manager to access frontend
    'urlManagerFE' => [
        'class' => 'yii\web\urlManager',
        'baseUrl' => '/advanced',
        'enablePrettyUrl' => true,
        'showScriptName' => false,
    ]
]
```