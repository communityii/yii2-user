Design for yii2-user
====================

## Assumptions and Considerations

1. The primary assumption is that the user module will have integrated social authentication inbuilt by default. 
2. The module will NOT have RBAC inbuilt.
3. Email integration using Yii swiftmail extension.
4. Social integration using Yii authclient extension.
5. All table names start with `adm_`. (any other suggestions)

## Plug & Play Features

The module needs to have the following configurable (plug and play components):

1. Enable/Disable Strength validation for password (can use my [yii2-password](https://demos.krajee.com/password) extension)
2. Enable/Disable social authentication for the module
3. Enable/Disable user roles for the module??? (Needs discussion)
4. Enable/Disable social profiles - Will use profile information from social provider to auto populate user data
5. Enable/Disable avatar support - will need a method to plugin to any image upload module or third party avatar 
   provider. Note the avatar also is available for each social profiles as in previous point 
6. Email templates (views) can be setup as parameters
7. Login Form Widget/View can be setup as parameters
8. Registration Form Widget/View can be setup as parameters
9. Password reset/recovery Widget/View can be setup as parameters
10. Admin CRUD Widget/Views can be setup as parameters
11. User Profile Page Views can be setup as parameters
12. Layout to use for Admin can be setup as parameters
13. Menu for admin can be setup as parameters
14. Layout to use for User Profile can be setup as parameters
15. Menu for user profile can be setup as parameters
16. User Status Configuration - DISCUSSION NEEDED - what all values are possible, and how access control needs to be set
17. User Role Configuration - DISCUSSION NEEDED - what all values are possible, and how access control needs to be set

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


### adm_user_identity

The remote user authentication identities.

```sql
CREATE TABLE `adm_user_identity` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'Unique remote user identifier',
	`key` VARCHAR(100) NOT NULL COMMENT 'Social provider identifier key',
	`provider` VARCHAR(30) NOT NULL DEFAULT 'Default' COMMENT 'Social provider code/name',
    `user_id` BIGINT(20) NOT NULL COMMENT 'Related user identifier',
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation time',
	`updated_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Record updation time',
	PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Remote identity authentication table for users';

ALTER TABLE `adm_user_identity`
ADD CONSTRAINT `adm_user_identity_FK1` 
FOREIGN KEY (`user_id`) 
REFERENCES `adm_user` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_user_identity_FK1` (`user_id` ASC);
```

### adm_user_profile

The master table for the user's main profile. Most of these fields are available as standard from what most social providers provide via OAuth/OAuth2.
This table can be edited/extended as needed by the developer for his/her application. The creation/updation of data in this table will need to be 
controlled via local/remote identity refresh configuration.

```sql
CREATE TABLE `adm_user_profile` (
	`id` BIGINT(20) NOT NULL COMMENT 'Unique remote user identifier',
	`profile_name` VARCHAR(150) COMMENT 'Social profile user name',
	`first_name` VARCHAR(50) DEFAULT '' COMMENT 'User first name',
	`last_name` VARCHAR(50) DEFAULT '' COMMENT 'User last name',
    `avatar_url` VARCHAR(255) DEFAULT '' COMMENT 'URL link to user avatar from the social provider.',
    `is_primary` TINYINT(1) DEFAULT FALSE COMMENT 'Whether this is the active/primary profile for display',
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation time',
	`updated_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Record updation time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User profile data including social provider details';

ALTER TABLE `adm_user_profile`
ADD CONSTRAINT `adm_user_profile_FK1` 
FOREIGN KEY (`id`) 
REFERENCES `adm_user_identity` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_user_profile_FK1` (`id` ASC);
```

### adm_role

The master table for all roles
```sql
CREATE TABLE `adm_role` (
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique role identifier',
	`name` VARCHAR(30) NOT NULL COMMENT 'Unique role name',
	`description` VARCHAR(30) NOT NULL COMMENT 'Description',
    `access_rules` TEXT COMMENT 'Needs discussion. Do we embed any json encoded access control rules here?',
	PRIMARY KEY (`id`),
	UNIQUE KEY `adm_user_UK1` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Role master table';
```

### adm_user_roles

Pivot table for user roles
```sql
CREATE TABLE `adm_user_role` (
	`user_id` BIGINT(20) NOT NULL COMMENT 'User identifier',
	`role_id` INT(11) NOT NULL COMMENT 'Role identifier',
	PRIMARY KEY (`user_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User master table';

ALTER TABLE `adm_user_role`
ADD CONSTRAINT `adm_user_role_FK1` 
FOREIGN KEY (`user_id`) 
REFERENCES `adm_user` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_user_role_FK1` (`user_id` ASC);

ALTER TABLE `adm_user_role`
ADD CONSTRAINT `adm_user_role_FK2` 
FOREIGN KEY (`role_id`) 
REFERENCES `adm_role` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_user_role_FK2` (`role_id` ASC);
```