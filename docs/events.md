Events
======

- [Registration Event](#registration-event)
- [Login Event](#login-event)

---

[:back: guide](index.md#key-concepts)

## Registration Event

**Namespace:** `comyii\user\events\RegistrationEvent`

The `RegistrationEvent` allows you to programmatically control your application workflow before and after an user account registration. 

[:back: guide](index.md#key-concepts)

### Registration Event Triggers

- Module::EVENT_REGISTER_BEGIN
- Module::EVENT_REGISTER_COMPLETE

[:back: top](#events) | 

### Registration Event Properties

|  **Property**   |        **Type**        | **Description**                                                                                                                                                             | **Default Value** |
|:----------------|:----------------------:|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|:-----------------:|
| **model**       | `ActiveRecord`         | the current user active record                                                                                                                                              | `NULL`            |
| **type**        | `string`               | the type of registration. This is used if there are multiple registration types (i.e. different user types)                                                                 | `NULL`            |
| **viewFile**    | `string`               | the main view file to be displayed.                                                                                                                                         | `NULL`            |
| **error**       | `string`               | the current status for the controller. This is used so that event handlers can tell the controller whether to not to continue. If set to `true` the registration will fail. | `NULL`            |
| **message**     | `string`               | the flash message for the controller. This is used so that event handlers can update the success messages for things like user registration.                                | `NULL`            |
| **flashType**   | `string`               | the flash message type                                                                                                                                                      | `NULL`            |
| **activate**    | `boolean`              | whether or not to activate the user account.                                                                                                                                | `false`           |
| **isActivated** | `boolean`              | the current user activation status                                                                                                                                          | `false`           |

[:back: top](#events) | [:back: guide](index.md#key-concepts)


======


---

[:back: guide](index.md#key-concepts)

## Login Event

**Namespace:** `comyii\user\events\LoginEvent`

The `LoginEvent` allows you to programmatically control your application workflow before and after an user account login. 

[:back: guide](index.md#key-concepts)

### Login Event Triggers

- Module::EVENT_LOGIN_BEGIN
- Module::EVENT_LOGIN_COMPLETE

[:back: top](#events) | 

### Login Event Properties

| property         | type           | description                                                                                                                                                                          | default              |
|------------------|----------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------|
| **user**         | `ActiveRecord` | the current user model                                                                                                                                                               |                      |
| **redirect**     | `string/array` | the URL to be redirected to                                                                                                                                                          |                      |
| **viewFile**     | `string`       | the view file to be rendered                                                                                                                                                         | `Module::VIEW_LOGIN` |
| **hasSocial**    | `boolean`      | has social authentication                                                                                                                                                            |                      |
| **authAction**   | `string`       | social authentication action                                                                                                                                                         |                      |
| **newPassword**  | `boolean`      | whether the password has been reset (read only)                                                                                                                                      |                      |
| **unlockExpiry** | `boolean`      | is account unlock attempt (read only)                                                                                                                                                |                      |
| **status**       | `string`       | the account status (read only)                                                                                                                                                       |                      |
| **result**       | `integer`      | the result of the login attempt (read only.) <br /> Set on Module::EVENT_LOGIN_COMPLETE<br /><ul><li>LoginEvent::RESULT_LOCKED</li><li>LoginEvent::RESULT_SUCCESS</li><li>LoginEvent::RESULT_FAIL</li><li>LoginEvent::RESULT_LOCKED</li><li>LoginEvent::RESULT_ALREADY_AUTH</li></ul> |                      |
| **message**      | `string`       | the flash message                                                                                                                                                                                                       |                      |
| **flashType**    | `string`       | the flash message type                                                                                                                                                                                                  |                      |
| **transaction**  | `boolean`      | whether or not to use transactions.                                                                                                                                                                                     | `true`               |
| **loginTitle**   | `string`       | login page title                                                                                                                                                                                                        |                      |
| **authTitle**    | `string`       | social auth login title                                                                                                                                                                                                 |                      |

[:back: top](#events) | [:back: guide](index.md#key-concepts)

