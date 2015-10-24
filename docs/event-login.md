##EventLogin

###Triggers
- *Module::EVENT_LOGIN_BEGIN*
- *Module::EVENT_LOGIN_COMPLETE*

###Properties
- **redirect** _string|array_ 
  - the URL to be redirected to.
- **viewFile**  _string|null_ 
 - the main view file to be rendered. If null then the default view file is used.
- **hasSocial** _boolean_ 
 - has social authentication.
- **authAction** _boolean_ 
 - social authentication action.
- **newPassword** _boolean_
 - whether the password has been reset. _(read only)_
- **unlockExpiry** _boolean_ 
 - is account unlock attempt. _(read only)_
- **user** _\commyii\user\models\User_ 
 - the current user model.
- **status** _string_ 
 - the account status. _(read only)_
- **result** _integer_ 
 - the result of the login attempt.  _(read only)_
  - LoginEvent::RESULT_SUCCESS
  - LoginEvent::RESULT_FAIL
  - LoginEvent::RESULT_LOCKED
  - LoginEvent::RESULT_ALREADY_AUTH
- **message** _string_ 
 - the flash message (unless redirect.)
- **flashType** _string_ 
 - the flash message type.
- **transaction** _boolean_
 - whether or not to use transactions.
- **loginTitle** _string_
 - login page title
- **authTitle** _string_
 - social auth login title
 
