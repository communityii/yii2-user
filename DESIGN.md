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
      - registered (user filled the form)
      - confirmed (user received an email an clicked on a link in the mail)
      - active (user can login, may be configured to happen automatically)
      - banned (user was blocked by an admin)
16. Events for register, confirm, active, banner, login, logout. We could trigger the e-mail delivery with these. Could be implemented in a decoupled way with Dependency Injection. Sample "Mailer" class can be added.

## Database Structure

### adm_user

The master table for users - contains the most basic fields for any user authentication.
```sql
CREATE TABLE `adm_user` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'Unique user identifier',
	`username` VARCHAR(255) NOT NULL COMMENT 'Unique user login name',
	`email` VARCHAR(255) NOT NULL COMMENT 'User email address',
	`password` VARCHAR(255) NOT NULL COMMENT 'Hashed password',
	`activation_key` VARCHAR(128) NOT NULL COMMENT 'Key to activate the account, sent by email',
	`reset_key` VARCHAR(128) COMMENT 'Key to reset user password',
	`status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'User status (e.g. registered, confirmed, activated, banned)',
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the user creation/registration',
    `last_login_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last login time',
	PRIMARY KEY (`id`),
	UNIQUE KEY `adm_user_UK1` (`username`),
	UNIQUE KEY `adm_user_UK2` (`email`),
	KEY `adm_user_NU1` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User master table';
```


### adm_remote_identity

The remote user authentication identities.

```sql
CREATE TABLE `adm_remote_identity` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'Unique remote user identifier',
	`profile_id` VARCHAR(100) NOT NULL COMMENT 'Social provider authorization identifier',
	`provider` VARCHAR(30) NOT NULL COMMENT 'Social provider code/name',
    `user_id` BIGINT(20) NOT NULL COMMENT 'Related user identifier',
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation time',
	`updated_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Record updation time',
	PRIMARY KEY (`id`),
    UNIQUE KEY `adm_user_UK1` (`provider`, `profile_id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Remote identity authentication table for users';

ALTER TABLE `adm_remote_identity`
ADD CONSTRAINT `adm_remote_identity_FK1` 
FOREIGN KEY (`user_id`) 
REFERENCES `adm_user` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_remote_identity_FK1` (`user_id` ASC);
```

### adm_user_profile

The user's main/primary profile data. Most of these fields are available as standard data from social providers using OAuth API.
This table can be edited/extended as needed by the developer for his/her application. 

The creation/updation of data in this table will need to be controlled via local/remote identity refresh configuration.

```sql
CREATE TABLE `adm_user_profile` (
	`id` BIGINT(20) NOT NULL COMMENT 'Unique user identifier',
	`profile_name` VARCHAR(180) COMMENT 'Social profile user name',
	`first_name` VARCHAR(60) DEFAULT '' COMMENT 'User first name',
	`last_name` VARCHAR(60) DEFAULT '' COMMENT 'User last name',
    `avatar_url` TEXT DEFAULT '' COMMENT 'URL link to user avatar from the social provider.',
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation time',
	`updated_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Record updation time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User profile data including social provider details';

ALTER TABLE `adm_user_profile`
ADD CONSTRAINT `adm_user_profile_FK1` 
FOREIGN KEY (`id`) 
REFERENCES `adm_user` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_user_profile_FK1` (`id` ASC);
```