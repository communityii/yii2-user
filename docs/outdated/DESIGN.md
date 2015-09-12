Design for yii2-user
====================

## Assumptions and Considerations

1. The primary assumption is that the user module will have integrated social authentication inbuilt by default. 
2. The module will NOT have RBAC inbuilt.
3. Email integration using Yii swiftmail extension.
4. Social-Auth integration using Yii authclient extension.
5. All tables are created with migrations and use database connection prefix from the application config.

## Plug & Play Features

The module needs to have the following configurable (plug and play components):

1. Enable/Disable Strength validation for password (can use my [yii2-password](https://demos.krajee.com/password) extension)
2. Enable/Disable social authentication for the module
3. Enable/Disable social profiles - Will use profile information from social provider to auto populate user data
4. Enable/Disable avatar support - will need a method to plugin to any image upload module or third party avatar 
   provider. Note the avatar also is available for each social profiles as in previous point 
5. Email templates (views) can be setup as parameters (see also 16.)
6. Login Form Widget/View can be setup as parameters
7. Registration Form Widget/View can be setup as parameters
8. Password reset/recovery Widget/View can be setup as parameters
9. Admin CRUD Widget/Views can be setup as parameters
10. User Profile Page Views can be setup as parameters
11. Menu for admin can be setup as parameters
12. Layout to use for Admin can be setup as parameters (maybe also with the Yii 2 path alias system only)
13. Layout to use for User Profile can be setup as parameters (maybe also with the Yii 2 path alias system only)
14. Menu for user profile can be setup as parameters
15. User Status Configuration - DISCUSSION NEEDED - what all values are possible, and how access control needs to be set
    - Possible statuses
      - pending (user created but pending confirmation)
      - active (user has been activated based on activation link confirmation - or via social authentication)
      - banned (user was blocked by an admin / moderator)
      - inactive (user has been inactivated - either due to multiple wrong password types or password expiry or other membership policy.)
16. Events for register, confirm, active, banned, login, logout. We could trigger the e-mail delivery with these. Could be implemented in a decoupled way with Dependency Injection. Sample "Mailer" class can be added.

## Database Structure

Refer the [migration code](https://github.com/communityii/yii2-user/blob/master/migrations/m140402_143411_create_user_module.php) for database structure.
You can also refer the [MYSQL SQL Script Source](https://github.com/communityii/yii2-user/blob/master/docs/db/mysql.sql) for more details.