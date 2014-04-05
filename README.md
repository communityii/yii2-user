yii2-user
=========

User module with inbuilt social authentication for Yii framework 2.0.

Refer the [design skeleton](https://github.com/communityii/yii2-user/blob/master/docs/DESIGN.md) for design discussion and planned features.

> NOTE: This module is under development and not ready for an alpha release yet.

## 1. Installation & Setup

### 1.1. Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require communityii\yii2-user "dev-master"
```

or add

```
"communityii\yii2-user": "dev-master"
```

to the ```require``` section of your `composer.json` file.

Run `$ php composer.phar update` from your console to get the latest version of the module packages.

### 1.2. User Module Setup
Setup the module in your configuration file like below

```php
'modules' => [
    'user' => [
        'class' => 'communityii\user\Module',
         // other module settings
    ],
    // your other modules
]
```

### 1.3. User Component Setup
Setup the user component in your configuration file to reflect the module login Url.

```php
'components' => [
    // user authentication component
    'user' => [
        'class' => 'yii\web\User',
        'loginUrl' => ['/user/account/login'],
    ],
    // your other modules
],
```

### 1.4. Database Connection Setup
Configure the database connection component `db` in your configuration file to reflect the right `tablePrefix`
as needed for your environment.

```php
'components' => [
    // database connection component
    'db' => [
        'class' => 'yii\db\Connection',
        // your table prefix
        'tablePrefix' => 'tbl_',
         // your connection settings
         dsn' => 'mysql:host=localhost;dbname=[DB_NAME]',
         'username' => '[SCHEMA_USERNAME]',
         'password' => '[SCHEMA_PASSWORD]',
         'charset' => 'utf8',
    ],
    // your other modules
],
```

### 1.5. Create User Database

The last part of the module setup is running the database migrations from console to setup the database schema
for the module.

```
$ php yii migrate/up --migrationPath=@vendor/communityii/yii2-user/migrations
```
