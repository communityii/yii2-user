DROP TABLE IF EXISTS `adm_mail_queue`;
DROP TABLE IF EXISTS `adm_user_ban_log`;
DROP TABLE IF EXISTS `adm_user_profile`;
DROP TABLE IF EXISTS `adm_remote_identity`;
DROP TABLE IF EXISTS `adm_user`;

CREATE TABLE `adm_user` (
	`id`                     BIGINT(20)   NOT NULL AUTO_INCREMENT COMMENT 'Unique user identifier',
	`username`               VARCHAR(255) NOT NULL	COMMENT 'Unique user login name',
	`email`                  VARCHAR(255) NOT NULL	COMMENT 'User email address',
	`password`               VARCHAR(255) NOT NULL	COMMENT 'Hashed password',
	`auth_key`               VARCHAR(128) NOT NULL	COMMENT 'Key for "remember me" authorization',
	`activation_key`         VARCHAR(128) NOT NULL	COMMENT 'Key to activate the account sent by email',
	`reset_key`              VARCHAR(128) COMMENT 'Key to reset user password',
	`status`                 TINYINT(1)   NOT NULL DEFAULT '0'	COMMENT 'User status (e.g. new, active, banned, inactive)',
	`password_fail_attempts` INT(11) DEFAULT '0'	COMMENT 'Password fail attempts',
	`last_login_ip`          VARCHAR(50) COMMENT 'Last login IP Address',
	`last_login_on`          TIMESTAMP    NOT NULL DEFAULT '0000-00-00 00:00:00'	COMMENT 'Last login time',
	`password_reset_on`      TIMESTAMP DEFAULT '0000-00-00 00:00:00'	COMMENT 'Password reset time',
	`created_on`             TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP	COMMENT 'Timestamp of the user creation/registration',
	`updated_on`             TIMESTAMP    NOT NULL DEFAULT '0000-00-00 00:00:00'	COMMENT 'Timestamp when user was updated',
	PRIMARY KEY (`id`),
	UNIQUE KEY `adm_user_UK1` (`username`),
	UNIQUE KEY `adm_user_UK2` (`email`),
	KEY `adm_user_NU1` (`status`)
)	ENGINE=InnoDB	DEFAULT CHARSET=utf8	COMMENT='User master table';

CREATE TABLE `adm_remote_identity` (
	`id`         BIGINT(20)   NOT NULL AUTO_INCREMENT COMMENT 'Unique remote user identifier',
	`profile_id` VARCHAR(100) NOT NULL	COMMENT 'Social provider authorization identifier',
	`provider`   VARCHAR(30)  NOT NULL	COMMENT 'Social provider code/name',
	`user_id`    BIGINT(20)   NOT NULL	COMMENT 'Related user identifier',
	`created_on` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP	COMMENT 'Record creation time',
	`updated_on` TIMESTAMP    NOT NULL DEFAULT '0000-00-00 00:00:00'	COMMENT 'Record update time',
	PRIMARY KEY (`id`),
	UNIQUE KEY `adm_user_UK1` (`provider`, `profile_id`)
)	ENGINE=InnoDB	DEFAULT CHARSET=utf8	COMMENT='Remote identity authentication table for users';

ALTER TABLE `adm_remote_identity`
ADD CONSTRAINT `adm_remote_identity_FK1`
FOREIGN KEY (`user_id`)
REFERENCES `adm_user` (`id`)
	ON DELETE CASCADE
	ON UPDATE RESTRICT
, ADD INDEX `adm_remote_identity_FK1` (`user_id` ASC);

CREATE TABLE `adm_user_profile` (
	`id`           BIGINT(20) NOT NULL	    COMMENT 'Unique user identifier',
	`profile_name` VARCHAR(180)             COMMENT 'Social profile user name',
	`first_name`   VARCHAR(60) DEFAULT ''	COMMENT 'User first name',
	`last_name`    VARCHAR(60) DEFAULT ''	COMMENT 'User last name',
	`avatar_url`   TEXT DEFAULT ''	        COMMENT 'URL link to user avatar from the social provider.',
	`created_on`   TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP	COMMENT 'Record creation time',
	`updated_on`   TIMESTAMP  NOT NULL DEFAULT '0000-00-00 00:00:00'
	COMMENT 'Record update time',
	PRIMARY KEY (`id`)
)	ENGINE=InnoDB	DEFAULT CHARSET=utf8	COMMENT='User profile data including social provider details';

ALTER TABLE `adm_user_profile`
ADD CONSTRAINT `adm_user_profile_FK1`
FOREIGN KEY (`id`)
REFERENCES `adm_user` (`id`)
	ON DELETE CASCADE
	ON UPDATE RESTRICT
, ADD INDEX `adm_user_profile_FK1` (`id` ASC);

CREATE TABLE `adm_user_ban_log` (
	`id`            BIGINT(20) NOT NULL	    COMMENT 'Unique user ban identifier',
	`user_ip`       VARCHAR(50)             COMMENT 'User IP Address at time of banning',
	`ban_reason`    VARCHAR(255)            COMMENT 'Ban reason',
	`revoke_reason` VARCHAR(255)            COMMENT 'Ban revoke reason',
	`user_id`       BIGINT(20) NOT NULL	    COMMENT 'Related user identifier',
	`created_on`    TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP	COMMENT 'Record creation time',
	`updated_on`    TIMESTAMP  NOT NULL DEFAULT '0000-00-00 00:00:00'	COMMENT 'Record update time',
	PRIMARY KEY (`id`)
)	ENGINE=InnoDB	DEFAULT CHARSET=utf8	COMMENT='User ban and revoke log';

ALTER TABLE `adm_user_ban_log`
ADD CONSTRAINT `adm_user_ban_log_FK1`
FOREIGN KEY (`id`)
REFERENCES `adm_user` (`id`)
	ON DELETE CASCADE
	ON UPDATE RESTRICT
, ADD INDEX `adm_user_ban_log_FK1` (`id` ASC);

CREATE TABLE `adm_mail_queue` (
	`id`         BIGINT(20)   NOT NULL	    COMMENT 'Unique mail queue identifier',
	`from_email` VARCHAR(255) NOT NULL	    COMMENT 'From email',
	`from_name`  VARCHAR(255)               COMMENT 'From name',
	`subject`    VARCHAR(255) NOT NULL	    COMMENT 'Email subject',
	`template`   VARCHAR(60)  NOT NULL	    COMMENT 'Email template to render',
	`user_id`    BIGINT(20)   NOT NULL	    COMMENT 'Related user identifier',
	`mail_log`   TEXT                       COMMENT 'Mailer log',
	`status`     TINYINT(1)   NOT NULL DEFAULT '0'                      COMMENT 'Email send status (e.g. queued, sent, error)',
	`created_on` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP        COMMENT 'Record creation time',
	`updated_on` TIMESTAMP    NOT NULL DEFAULT '0000-00-00 00:00:00'    COMMENT 'Record update time',
	PRIMARY KEY (`id`)
)	ENGINE=InnoDB	DEFAULT CHARSET=utf8	COMMENT='Mail queue for notifications to user';

ALTER TABLE `adm_mail_queue`
ADD CONSTRAINT `adm_mail_queue_FK1`
FOREIGN KEY (`id`)
REFERENCES `adm_user` (`id`)
	ON DELETE CASCADE
	ON UPDATE RESTRICT
, ADD INDEX `adm_mail_queue_FK1` (`id` ASC);