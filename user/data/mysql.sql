DROP TABLE IF EXISTS `adm_user_profile`;
DROP TABLE IF EXISTS `adm_remote_identity`;
DROP TABLE IF EXISTS `adm_user`;

CREATE TABLE `adm_user` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'Unique user identifier',
	`username` VARCHAR(255) NOT NULL COMMENT 'Unique user login name',
	`email` VARCHAR(255) NOT NULL COMMENT 'User email address',
	`password` VARCHAR(255) NOT NULL COMMENT 'Hashed password',
	`role` VARCHAR(30) NOT NULL DEFAULT 'user' COMMENT 'User role',
	`auth_key` VARCHAR(128) NOT NULL COMMENT 'Key for "remember me" authorization',
	`activation_key` VARCHAR(128) NOT NULL COMMENT 'Key to activate the account sent by email',
	`reset_key` VARCHAR(128) COMMENT 'Key to reset user password',
	`status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'User status (e.g. registered, confirmed, active, banned, inactive)',
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the user creation/registration',
    `last_login_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last login time',
	PRIMARY KEY (`id`),
	UNIQUE KEY `adm_user_UK1` (`username`),
	UNIQUE KEY `adm_user_UK2` (`email`),
	KEY `adm_user_NU1` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User master table';

CREATE TABLE `adm_remote_identity` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'Unique remote user identifier',
	`profile_id` VARCHAR(100) NOT NULL COMMENT 'Social provider authorization identifier',
	`provider` VARCHAR(30) NOT NULL COMMENT 'Social provider code/name',
    `user_id` BIGINT(20) NOT NULL COMMENT 'Related user identifier',
	`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation time',
	`updated_on` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Record updation time',
	PRIMARY KEY (`id`),
    UNIQUE KEY `adm_user_UK1` (`provider`, `profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Remote identity authentication table for users';

ALTER TABLE `adm_remote_identity`
ADD CONSTRAINT `adm_remote_identity_FK1`
FOREIGN KEY (`user_id`)
REFERENCES `adm_user` (`id`)
ON DELETE CASCADE
ON UPDATE RESTRICT
, ADD INDEX `adm_remote_identity_FK1` (`user_id` ASC);

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