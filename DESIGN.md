Design for yii2-user
====================

** Assumptions and Considerations

1. The primary assumption is that the user module will have integrated social authentication inbuilt by default. 
2. The module will NOT have RBAC inbuilt.
3. Email integration using Yii swiftmail extension.
4. Social integration using Yii authclient extension.
5. All table names start with `adm_`. (any other suggestions)

** Plug & Play Features

The module needs to have the following configurable (plug and play components):
1. Enable/Disable social authentication for the module
2. Enable/Disable user roles for the module??? (Needs discussion)
3. Enable/Disable social profiles - Will use profile information from social provider to auto populate user data
4. Enable/Disable avatar support - will need a method to plugin to any image upload module or third party avatar 
   provider. Note the avatar also is available for each social profiles as in previous point 
5. Email templates (views) can be setup as parameters
6. Login Form Widget/View can be setup as parameters
7. Registration Form Widget/View can be setup as parameters
8. Admin CRUD Widget/Views can be setup as parameters
9. User Profile Page Views can be setup as parameters
10. Layout to use for Admin can be setup as parameters
11. Menu for admin can be setup as parameters
12. Layout to use for User Profile can be setup as parameters
13. Menu for user profile can be setup as parameters
14. User Status Configuration - DISCUSSION NEEDED - what all values are possible, and how access control needs to be set
15. User Role Configuration - DISCUSSION NEEDED - what all values are possible, and how access control needs to be set

** Database Structure

*** adm_user

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
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the registration',
    `last_login_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last login time',
	PRIMARY KEY (`id`),
	UNIQUE KEY `adm_user_UK1` (`username`),
	UNIQUE KEY `adm_user_UK2` (`email`),	
	KEY `adm_user_NU1` (`role`),
	KEY `adm_user_NU2` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User master table';
```

*** adm_user_profile

The master table for user profile. Most of these fields are available as standard from what most social providers provide via OAuth/OAuth2. 
There should be one record in this table always when a new user registers without social auth (The provider name will be DEFAULT).
Note some of these special fields:
- is_active: the default profile to be used for the user. If an user has multiple social providers he has authenticated with, all these
  will appear for his account, and he can choose which social profile details he can set to default. He/she can also override/edit these
  profile details within the module.
- avatar_linked: If set to TRUE, it will use the social provider avatar from the user.
```sql
CREATE TABLE `adm_user_profile` (
	`id` VARCHAR(70) NOT NULL COMMENT 'Unique profile identifier',
	`profile_id` VARCHAR(50) NOT NULL COMMENT 'Unique user ID on the connected provider (ID, Email, URL, etc.). Defaulted to user id if not related to provider.',
	`provider` VARCHAR(20) NOT NULL DEFAULT 'Default' COMMENT 'Social provider code/name',
	`user_id` BIGINT(20) NOT NULL COMMENT 'Related user identifier',
	`profile_url` VARCHAR(255) DEFAULT '' COMMENT 'URL link to profile page on the IDp web site',
	`website_url` VARCHAR(255) DEFAULT '' COMMENT 'User website, blog, web page',
	`avatar_linked` TINYINT(1) DEFAULT FALSE COMMENT 'Is the user photo/avatar a link to external location OR an uploaded file.',
	`avatar_file` VARCHAR(255) DEFAULT '' COMMENT 'File name of the uploaded avatar.',
	`avatar_link` VARCHAR(255) DEFAULT '' COMMENT 'URL link to user photo or avatar if avatar is linkable.',
	`display_name` VARCHAR(100) DEFAULT '' COMMENT 'User display name provided by the IDp or a concatenation of first and last name.',
	`description` TINYTEXT DEFAULT '' COMMENT 'A short about me for the user',
	`first_name` VARCHAR(50) DEFAULT '' COMMENT 'User first name',
	`last_name` VARCHAR(50) DEFAULT '' COMMENT 'User last name',
	`gender` VARCHAR(10) DEFAULT '' COMMENT 'User gender. Values are "female", "male" or NULL',
	`language` VARCHAR(10) DEFAULT 'en' COMMENT 'User language',
	`birth_date` DATE COMMENT 'Birth date of the user',
	`email` VARCHAR(255) DEFAULT '' COMMENT 'User email. Not all of IDp grant access to the user email',
	`email_verified` VARCHAR(255) DEFAULT '' COMMENT 'Verified user email. Not all of IDp grant access to verified user email. ',
	`phone` VARCHAR(30) DEFAULT '' COMMENT 'User phone number',
	`address` VARCHAR(255) DEFAULT '' COMMENT 'User address',
	`country` VARCHAR(50) DEFAULT '' COMMENT 'User country',
	`region` VARCHAR(50) DEFAULT '' COMMENT 'User region/state/province',
	`city` VARCHAR(50) DEFAULT '' COMMENT 'User city',
	`zip` VARCHAR(10) DEFAULT '' COMMENT 'User postal code/zip',
	`is_active` TINYINT(1) DEFAULT FALSE COMMENT 'Active user profile visible to all',
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation time',
	`created_by` BIGINT(20) NOT NULL COMMENT 'Record created by',
	`updated_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Record updation time',
	`updated_by` BIGINT(20) NOT NULL COMMENT 'Record updated by',
	PRIMARY KEY (`id`),
	UNIQUE KEY `adm_user_profile_UQ1`(`profile_id`, `provider`),
	KEY `adm_user_profile_NU1` (`profile_id`),
	KEY `adm_user_profile_NU2` (`provider`),
	KEY `adm_user_profile_NU3` (`user_id`),
	KEY `adm_user_profile_NU4` (`first_name`),
	KEY `adm_user_profile_NU5` (`last_name`),
	KEY `adm_user_profile_NU6` (`display_name`),
	KEY `adm_user_profile_NU7` (`email`),
	KEY `adm_user_profile_NU8` (`email_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User profile data including social provider details';

ALTER TABLE `adm_user_profile`
ADD CONSTRAINT `adm_user_profile_FK1` 
FOREIGN KEY (`user_id`) 
REFERENCES `adm_user` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_user_profile_FK1` (`user_id` ASC);

ALTER TABLE `adm_user_profile`
ADD CONSTRAINT `adm_user_profile_FK2` 
FOREIGN KEY (`created_by`) 
REFERENCES `adm_user` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_user_profile_FK2` (`created_by` ASC);

ALTER TABLE `adm_user_profile`
ADD CONSTRAINT `adm_user_profile_FK3` 
FOREIGN KEY (`updated_by`) 
REFERENCES `adm_user` (`id`) 
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_user_profile_FK3` (`updated_by` ASC);
```

*** adm_role

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

*** adm_user_roles

Pivot table for user roles
```sql
CREATE TABLE `adm_user_role` (
	`user_id` BIGINT(20) NOT NULL COMMENT 'User identifier',
	`role_id` INT(11) NOT NULL COMMENT 'Role identifier',
	PRIMARY KEY (`user_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User master table';
```