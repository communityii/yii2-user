yii2-user
=========

A configurable user management module for Yii framework 2.0 with inbuilt social authentication and various user management controls. The module has been intentionally kept simple - yet enable yii developers to achieve user management & control for various use cases and scenarios. The module does not have RBAC (Role Based Access Control) inbuilt as an intention to keep it simple. There are few access control methods that help achieve various use cases. However, for building advanced / comple specific use cases, almost every feature has been enabled to be configurable and extensible by the developer. Be it the module rules & settings, the model classes, the controller actions, the layouts, or the views - each of these can be customized. In addition few reusable components and widgets have been provided for expanding and building further use cases.

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

By default users are placed in pending status when registered. This will not be applicable if you have `autoActivate` setting to `false` in your `registrationSettings`

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

#### _more concepts and usage information to be added later in detailed documentation_

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
Setup the module in your configuration file like below

```php
'modules' => [
    'user' => [
        'class' => 'comyii\user\Module',
        //  other module settings
    ],
   //  your other modules
]
```

### User Component Setup
Setup the user component in your configuration file for the yii2-user module.

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
Configure the database connection component `db` in your configuration file to reflect the right `tablePrefix`
as needed for your environment.

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

The last part of the module setup is running the database migrations from console to setup the database schema
for the module.

```
$ php yii migrate/up --migrationPath=@vendor/communityii/yii2-user/migrations
```

### Getting started

Login to the url pertaining to the user module path (e.g. `http://localhost/app/user`) and you should be automatically guided with an installation wizard. Finish the superuser setup to proceed using the module.
