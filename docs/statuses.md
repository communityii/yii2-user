User Statuses
=============

- [Primary Statuses](#primary-statuses)
- [Secondary Statuses](#secondary-statuses)

---

[:back: guide](index.md#key-concepts)

## Primary Statuses

The primary statuses control the immediate level of access for each user to the application as well as the ability to perform various CRUD operations. The primary status is a mandatory data that must exist for every user in the application. The primary status is stored in the `status` field in the user table in the database.

### Superuser

- **Type:** System Status.
- **Editing:** No users can edit this status.
- **Identifier:** `Module::STATUS_SUPERUSER`

A superuser is the first user created by default on install. The superuser is virtually the **GOD** :angel: for the application and has all accesses inherited by default. There can exist only ONE superuser typically (though you could create additional superusers within the database table if you need). Superusers cannot be modified by other users except the superuser himself / herself. Ability to modify a superuser account is controlled by `Module::superuserEditSettings`.

---

[:back: top](#user-statuses) | [:back: guide](index.md#key-concepts)

### Admin

- **Type:** Editable Status.
- **Editing:** No users except admin or superuser can edit this status.
- **Identifier:** `Module::STATUS_ADMIN`

Admin users must be created by either the superuser or other administrators. An existing active user can also be converted to an admin user. Administrators behave like superusers to some extent. The exception is that admin users can be inactivated or manipulated by the superuser or other administrators based on module level settings. Ability to modify an admin account is controlled by `Module::adminEditSettings`.

---

[:back: top](#user-statuses) | [:back: guide](index.md#key-concepts)

### Active

- **Type:** Editable Status.
- **Editing:** No users except admin or superuser can edit this status.
- **Identifier:** `Module::STATUS_ACTIVE`

The active status determines whether the user account is active and allowed access to the application. Admin and Superuser have accesses to application by default. The active status determines the active status for normal users (other than admin or superuser). Ability to modify a normal user account record is controlled by `Module::userEditSettings`.

---

[:back: top](#user-statuses) | [:back: guide](index.md#key-concepts)

### Inactive

- **Type:** Editable Status.
- **Editing:** No users except admin or superuser can edit this status.
- **Identifier:** `Module::STATUS_ACTIVE`

Whether the user is inactive (banned / deleted). No access will be allowed for inactive users to the application. Admin and Superuser can however view / manage inactive users.

---

[:back: top](#user-statuses) | [:back: guide](index.md#key-concepts)

### Pending

- **Type:** System Status.
- **Editing:** No users can edit this status.
- **Identifier:** `Module::STATUS_PENDING`

This status identifies that a new user registration is pending activation. By default users are placed in **pending** status when registered. This will not be applicable if you have `autoActivate` setting to `true` in your `Module::registrationSettings`.

---

[:back: top](#user-statuses) | [:back: guide](index.md#key-concepts)

## Secondary Statuses

The secondary statuses identify whether any user account has additional actions pending that control his / her access to the application. Thus, these statuses exist in addition to the primary status of the user and is not mandatory for every user. Except for the `Inactive` primary status, the secondary statuses can be combined with any of the other primary statuses above. Currently the module offers inbuilt support for two secondary statuses - `Expired` and `Locked`. The secondary status is stored in the `status_sec` field in the user table in the database.

### Expired

- **Type:** System Status.
- **Editing:** No users can edit this status.
- **Identifier:** `Module::STATUS_EXPIRED`

Whenever a password for an user account expires, the user is placed into this additional status, and guided to changing his / her password. This feature can be controlled by configuring `passwordExpiry` in `Module::passwordSettings`. This status is validated for every user irrespective of their primary status. The only exception is if the user's primary status is  `Inactive`. The `Inactive` users are never allowed access to the application unless they have one of the other primary statuses.

---

[:back: top](#user-statuses) | [:back: guide](index.md#key-concepts)

### Locked

- **Type:** System Status.
- **Editing:** No users can edit this status.
- **Identifier:** `Module::STATUS_LOCKED`

Whenever an user types a wrong password for a specified number of times consecutively - his / her account is locked. This is controlled by configuring `wrongAttempts` in  `Module::passwordSettings`. This status is validated for every user irrespective of their primary status. The only exception is if the user's primary status is  `Inactive`. The `Inactive` users are never allowed access to the application unless they have one of the other primary statuses.

---

[:back: top](#user-statuses) | [:back: guide](index.md#key-concepts)