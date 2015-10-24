Installation and Setup
======================

- [Installation](#installation)
- [User Module Setup](#user-module-setup)
- [User Component Setup](#user-component-setup)
- [Database Connection Setup](#database-connection-setup)
- [Create User Database](#create-user-database)
- [Setup Mailer Component](#setup-mailer-component)
- [Setup Social Component](#setup-social-component)
- [Setup Profile](#setup-profile)
    - [Setup Base Path](#setup-base-path)
    - [Setup Base URL](#setup-base-url)
    - [Setup Default Avatar](#setup-default-avatar)
- [Using the module](#using-the-module)
- [Usage with Yii 2 Advanced Template](#usage-with-yii-2-advanced-template)
    - [Setup Session Storage](#setup-session-storage)
    - [Frontend Configuration](#frontend-configuration)
    - [Backend Configuration](#backend-configuration)

---

[:back: guide](index.md#getting-started)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/ ).

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

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## User Module Setup

Setup the module in your configuration file as shown below. The module depends on and uses the [yii2-grid module](https://demos.krajee.com/grid), so you must also setup the `gridview` module also as shown below. You can set this within `common/config/main.php` if using with Yii 2 advanced app or within `config/web.php` for Yii 2 basic app. There are various settings available for configuring your user module. Read through the documentation to understand and set these carefully for your application.

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

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## User Component Setup

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

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## Database Connection Setup

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

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## Create User Database

Run the database migrations from your command console to setup the database schema for the module.

```
$ php yii migrate/up --migrationPath=@vendor/communityii/yii2-user/migrations
```

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## Setup Mailer Component

The module uses [yii2-swiftmailer](https://github.com/yiisoft/yii2-swiftmailer) extension to generate emails to the users for various actions. You would need to configure the mailer component as shown below. For example, the configuration file would be `common\config\main-local.php` if you are using the Yii 2 advanced app. Note any `viewPath` set within this component will be ignored by the `yii2-user` module. Instead, the setting within `comyii\user\Module::notificationSettings['viewPath']` will be considered for parsing the mailer templates within this module.

```php
'components' => [
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@common/mail',
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

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## Setup Social Component

 _Optional Setup._
 
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

In addition, you must setup the `Module::socialSettings` property to control the social authentication configuration for this module. For example, you can modify and set the `socialSettings` to what you need within user module settings.

```php
'modules' => [
    'user' => [ 
        'socialSettings' => [
            // whether social authentication is enabled
            'enabled' => true,
            // whether social client choices will be displayed on login, register and profile
            'widgetEnabled' => true,
            // the widget class to use to render the social client choices
            'widgetSocialClass' => 'comyii\user\widgets\SocialConnects',
            // the widget settings
            'widgetSocial' => []
        ],
    ],
],
//  your other modules
```

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## Setup Profile

_Optional Setup._
 
The profile settings for the module can be enabled and configured via the `Module::profileSettings['enabled']` property, which is `true` by default.  In order to begin using the module for profile avatar upload, carry out the following steps. 

### Setup Base Path

Create a folder on your application where profile avatar images will be stored. Then configure your `Module::profileSettings['basePath']` to point to this folder. This by default is set to `@webroot/uploads`, so you can directly create an `uploads` folder on your web root and proceed.

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

### Setup Base URL

Identify a web accessible URL for the folder above and set `Module::profileSettings['baseUrl']` to point to this. It is recommended to set the absolute URL here, so that the images are accessible across both frontend and backend apps (if using with Yii 2 advanced application template).

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

### Setup Default Avatar

Setup a default avatar image file to be displayed when no user profile picture is found. Copy an image file `avatar.png` or any filename you wish to the above uploads path. Configure `Module::profileSettings['defaultAvatar']` to this filename. For example, all the steps above would look like this in the configuration file.

```php
// module settings in Yii config file
// @common/config/main.php
'modules' => [
    'user' => [
        'profileSettings' => [
            'enabled' => true
            'basePath' => '@webroot/uploads',
            'baseUrl' => 'http://localhost/app/uploads', // absolute URL
            'defaultAvatar' => 'avatar.png', // a file in above location
        ]
    ]
]
```

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## Using the module

Login to the url pertaining to the user module path (e.g. `http://localhost/app/user`) and you should be automatically guided with an installation wizard. The steps to create a superuser are self-explanatory. Finish the superuser setup to proceed using the module. If you have `prettyUrl` enabled in your `urlManager`, the Module will automatically set some user friendly URL to access, which you can modify via `Module::urlRules` and `Module::urlPrefix`.

The following default links will be available for use if your app root is available at `http://localhost/app`. You may wish to alter your application menus to point to these.

Link                    | URL
------------------------| -------------------------------------
User Login              | http://localhost/app/user/login
User Logout             | http://localhost/app/user/logout
User Register           | http://localhost/app/user/register
User Password Change    | http://localhost/app/user/password
User Password Recovery  | http://localhost/app/user/recovery
User Password Reset     | http://localhost/app/user/reset/KEY
User Activate           | http://localhost/app/user/activate/KEY
User Email Change       | http://localhost/app/user/newemail/KEY
User Social Auth        | http://localhost/app/user/auth/CLIENT
User Profile            | http://localhost/app/user/profile
User Profile Update     | http://localhost/app/user/profile/update
User Profile Admin Mode | http://localhost/app/user/profile/ID
Admin Users List        | http://localhost/app/user/admin
Admin User View         | http://localhost/app/user/ID
Admin User Create       | http://localhost/app/user/create
Admin User Update       | http://localhost/app/user/update/ID

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

## Usage with Yii 2 Advanced Template

You can configure the module to work easily with the [Yii 2 Advanced Application Template](https://github.com/yiisoft/yii2-app-advanced) and have different sessions for frontend and backend of the Yii 2 advanced app. For this you may carry out the following steps:

### Setup Session Storage

Create the following folders in your Yii 2 advanced app.

- `frontend/runtime/sessions`
- `backend/runtime/sessions`

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

### Frontend Configuration

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

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)

### Backend Configuration

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

---

[:back: top](#installation-and-setup) | [:back: guide](index.md#getting-started)