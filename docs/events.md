Events
======

- [Registration Event](#registration-event)

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

