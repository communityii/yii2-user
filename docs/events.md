Events
======

- [Registration Event](#registration-event)
    - [Registration Event Triggers](#registration-event-triggers)
    - [Registration Event Properties](#registration-event-properties)
- [Login Event](#login-event)
    - [Login Event Triggers](#login-event-triggers)
    - [Login Event Properties](#login-event-properties)

---

[:back: guide](index.md#key-concepts)

The module triggers a few [Yii Events](http://www.yiiframework.com/doc-2.0/guide-concept-events.html) that allow advanced programming and custom code to be injected. The following events are available via the `comyii\user\Module` class:

- `Module::EVENT_BEFORE_ACTION` or `beforeAction`
- `Module::EVENT_REGISTER_BEGIN` or `beforeRegister`
- `Module::EVENT_REGISTER_COMPLETE` or `registerComplete`
- `Module::EVENT_LOGIN_BEGIN` or `loginBegin`
- `Module::EVENT_LOGIN_COMPLETE` or `loginComplete`
- `Module::EVENT_LOGOUT` or `logout`
- `Module::EVENT_PASSWORD_BEGIN` or `passwordBegin`
- `Module::EVENT_PASSWORD_COMPLETE` or `passwordComplete`
- `Module::EVENT_RECOVERY_BEGIN` or `recoveryBegin`
- `Module::EVENT_RECOVERY_COMPLETE` or `recoveryComplete`
- `Module::EVENT_RESET_BEGIN` or `resetBegin`
- `Module::EVENT_RESET_COMPLETE` or `resetComplete`
- `Module::EVENT_ACTIVATE_BEGIN` or `activateBegin`
- `Module::EVENT_ACTIVATE_COMPLETE` or `activateComplete`
- `Module::EVENT_AUTH_BEGIN` or `authBegin`
- `Module::EVENT_AUTH_COMPLETE` or `authComplete`
- `Module::EVENT_NEWEMAIL_BEGIN` or `newemailBegin`
- `Module::EVENT_NEWEMAIL_COMPLETE` or `newemailComplete`
- `Module::EVENT_EXCEPTION` or `exception`

## Registration Event

**Namespace:** `comyii\user\events\RegistrationEvent`

The `RegistrationEvent` allows you to programmatically control your application workflow before and after an user account registration. 

[:back: top](#events) | [:back: guide](index.md#key-concepts)

### Registration Event Triggers

- `Module::EVENT_REGISTER_BEGIN`
- `Module::EVENT_REGISTER_COMPLETE`

[:back: top](#events) | [:back: guide](index.md#key-concepts)

### Registration Event Properties

#### model

- **Data Type:** [_yii\db\ActiveRecord_](http://www.yiiframework.com/doc-2.0/yii-db-activerecord.html)
- **Default Value:** `NULL`

The current user active record.

#### redirectUrl

- **Data Type:** `string|array`
- **Default Value:** `Yii::$app->user->returnUrl`

The URL to be redirected to - will default automatically to the `returnUrl`. See `comyii\controllers\BaseController::safeRedirect` method.

#### viewFile

- **Data Type:** _string_
- **Default Value:** `Module::viewSettings[Module::VIEW_REGISTER]`

The main view file to be displayed. If this is `NULL` or not set the default view file (`views/account/register`) will be used for display as set in `Module::viewSettings[Module::VIEW_REGISTER]`.

#### error

- **Data Type:** _boolean_
- **Default Value:** `false`

The current status for the controller. This is necessary for event handlers to communicate to the controller whether to not abort the registration process. If set to `true`, the registration will fail.

#### flashType

- **Data Type:** _string_
- **Default Value:** `NULL`

The session flash message type as used by `Yii::$app->session->setFlash`.

#### message

- **Data Type:** _string_
- **Default Value:** `NULL`

The session flash message for the controller passed via `Yii::$app->session->setFlash`. This is used so that event handlers can update the success messages for scenarios like user registration. 

#### type

- **Data Type:** _string_
- **Default Value:** `NULL`

The user type for registration. This is used if there are multiple registration types (i.e. different [user types](user-types.md)).

#### activate

- **Data Type:** _boolean_
- **Default Value:** `false`

Whether or not to activate the user account.

#### isActivated

- **Data Type:** _boolean_
- **Default Value:** `false`

The current user activation status.

[:back: top](#events) | [:back: guide](index.md#key-concepts)


## Login Event

**Namespace:** `comyii\user\events\LoginEvent`

The `LoginEvent` allows you to programmatically control your application workflow before and after an user account login. 

[:back: top](#events) | [:back: guide](index.md#key-concepts)

### Login Event Triggers

- `Module::EVENT_LOGIN_BEGIN`
- `Module::EVENT_LOGIN_COMPLETE`

### Login Event Properties

#### model

- **Data Type:** [_yii\db\ActiveRecord_](http://www.yiiframework.com/doc-2.0/yii-db-activerecord.html)
- **Default Value:** `NULL`

The current user active record (if logged in) or the login model (before login).

#### redirectUrl

- **Data Type:** `string|array`
- **Default Value:** `Yii::$app->user->returnUrl`

The URL to be redirected to - will default automatically to the `returnUrl`. See `comyii\controllers\BaseController::safeRedirect` method.

#### viewFile

- **Data Type:** _string_
- **Default Value:** `Module::viewSettings[Module::VIEW_LOGIN]`

The main view file to be displayed. If this is `NULL` or not set the default view file (`views/account/login`) will be used for display as set in `Module::viewSettings[Module::VIEW_LOGIN]`.

#### error

- **Data Type:** _boolean_
- **Default Value:** `false`

The current status for the controller. This is necessary for event handlers to communicate to the controller whether to not abort the login process.

#### flashType

- **Data Type:** _string_
- **Default Value:** `NULL`

The session flash message type as used by `Yii::$app->session->setFlash`.

#### message

- **Data Type:** _string_
- **Default Value:** `NULL`

The session flash message for the controller passed via `Yii::$app->session->setFlash`. This is used so that event handlers can update the success messages for scenarios like user registration. 

#### hasSocial

- **Data Type:** _boolean_
- **Default Value:** `Module::socialSettings['enabled']`

Whether social authentication is enabled. If not set, this defaults to the `Module::socialSettings` setting.

#### authAction

- **Data Type:** _string|array_
- **Default Value:** `false`

The social authentication action. If not set, defaults to `Module::ACTION_SOCIAL_AUTH` set within `Module::actionSettings`.

#### unlockExpiry

- **Data Type:** _boolean_
- **Default Value:** `false`

Whether an account unlock attempt after expiry. Defaults to `false`.

#### newPassword

- **Data Type:** _boolean_
- **Default Value:** `false`

Whether a new password has been set after expiry. Defaults to `false`.

#### status

- **Data Type:** _integer_
- **Default Value:** `NULL`

The account status at login. Should be one of the `Module::STATUS` constants.

#### loginTitle

- **Data Type:** _string_
- **Default Value:** `NULL`

The login section title.

#### authTitle

- **Data Type:** _string_
- **Default Value:** `NULL`

The social auth login section title.

#### authTitle

- **Data Type:** _integer_
- **Default Value:** `NULL`

The result of the login attempt. Should be one of the values below:

- `LoginEvent::RESULT_SUCCESS` or `1`
- `LoginEvent::RESULT_FAIL` or `2`
- `LoginEvent::RESULT_LOCKED` or `3`
- `LoginEvent::RESULT_ALREADY_AUTH` or `4`
- `LoginEvent::RESULT_EXPIRED` or `5`

[:back: top](#events) | [:back: guide](index.md#key-concepts)