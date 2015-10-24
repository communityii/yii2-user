##RegistrationEvent


###Triggers
- Module::EVENT_REGISTER_BEGIN
- Module::EVENT_REGISTER_COMPLETE


###Properties
- **model** the current user active record.
- **type** the type of registration. This is used if there are multiple registration types (ie. different user types)
- **viewFile** the main view file to be displayed.
- **error** the current status for the controller. This is used so that event handlers can tell the controller whether to not to continue. If set
to true the registration will fail.
- **message** the flash message for the controller. This is used so that event handlers can update the success messages for things like user registration.
- **flashType** the flash message type
- **activate** whether or not to activate the user account.
- **isActivated** the current user activation status.
