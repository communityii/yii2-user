Events
======

- [Event Triggers](#event-triggers)
- [Registration Event](#registration-event)
    - [Registration Event Triggers](#registration-event-triggers)
    - [Registration Event Properties](#registration-event-properties)

---

[:back: guide](index.md#key-concepts)

## Event Triggers

The module triggers a few Yii events for advanced programming. The following event triggers are available:

### `Module::EVENT_BEFORE_ACTION`

This event is triggered before any action is executed by any controller in the `communityii/yii2-user` module.

### `Module::EVENT_REGISTER_BEGIN`

This event is triggered when the `register` action is initiated for the user.

### `Module::EVENT_REGISTER_COMPLETE` 

This event is triggered after the `register` action is completed for the user.

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

The current user active record

#### type

- **Data Type:** _string_
- **Default Value:** `NULL`

The type of registration. This is used if there are multiple registration types (i.e. different [user types](user-types.md)).

#### viewFile

- **Data Type:** _string_
- **Default Value:** `NULL`

The main view file to be displayed.

#### error

- **Data Type:** _boolean_
- **Default Value:** `false`

The current status for the controller. This is necessary for event handlers to communicate to the controller whether to not abort the registration process. If set to `true`, the registration will fail.

#### message

- **Data Type:** _string_
- **Default Value:** `NULL`

The session flash message for the controller passed via `Yii::$app->session->setFlash`. This is used so that event handlers can update the success messages for scenarios like user registration. 

#### flashType

- **Data Type:** _string_
- **Default Value:** `NULL`

The session flash message type as used by `Yii::$app->session->setFlash`.

#### activate

- **Data Type:** _boolean_
- **Default Value:** `false`

Whether or not to activate the user account.

#### isActivated

- **Data Type:** _boolean_
- **Default Value:** `false`

The current user activation status.

[:back: top](#events) | [:back: guide](index.md#key-concepts)

