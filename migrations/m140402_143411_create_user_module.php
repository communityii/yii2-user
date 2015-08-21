<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\db\Schema;
use comyii\user\Module;

/**
 * Migration for creating the database structure for the comyii\yii2-user module.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class m140402_143411_create_user_module extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // Table # 1: User
        $this->createTable('{{%user}}', [
            'id' => $this->bigPrimaryKey(),
            'username' => $this->string(30)->notNull(),
            'email' => $this->string(255)->notNull(),
            'password' => $this->string(30)->notNull(),
            'auth_key' => $this->string(128)->notNull(),
            'activation_key' => $this->string(128),
            'reset_key' => $this->string(128),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'password_fail_attempts' => $this->smallInteger()->defaultValue(0),
            'last_login_ip' => $this->string(50),
            'last_login_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'password_reset_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'created_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updated_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
        $this->createIndex('user_username_uk', '{{%user}}', 'username', true);
        $this->createIndex('user_email_uk', '{{%user}}', 'email', true);
        $this->createIndex('user_status_nu', '{{%user}}', 'status');

        // Table # 2: Remote identity
        $this->createTable('{{%remote_identity}}', [
            'id' => $this->bigInteger(),
            'profile_id' => $this->string(128)->notNull(),
            'provider' => $this->string(30)->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'created_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updated_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
        $this->addForeignKey('remote_identity_fk', '{{%remote_identity}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->createIndex('remote_identity_nu', '{{%remote_identity}}', 'user_id');

        // Table # 3: User profile
        $this->createTable('{{%user_profile}}', [
            'id' => $this->bigInteger()->notNull(),
            'display_name' => $this->string(120),
            'first_name' => $this->string(60),
            'last_name' => $this->string(60),
            'avatar_url' => $this->text(),
            'created_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updated_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
        $this->addPrimaryKey('user_profile_pk', '{{%user_profile}}', 'id');
        $this->addForeignKey('user_profile_fk', '{{%user_profile}}', 'id', '{{%user}}', 'id', 'CASCADE');

        // Table # 4: User ban log
        $this->createTable('{{%user_ban_log}}', [
            'id' => $this->bigPrimaryKey(),
            'user_ip' => $this->string(60),
            'ban_reason' => $this->string(255),
            'revoke_reason' => $this->string(255),
            'user_id' => $this->bigInteger()->notNull(),
            'banned_till' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'created_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updated_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
        $this->addForeignKey('user_ban_log_fk', '{{%user_ban_log}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->createIndex('user_ban_log_nu}}', '{{%user_ban_log}}', 'user_id');

        // Table # 5: Mail queue
        $this->createTable('{{%mail_queue}}', [
            'id' => $this->bigPrimaryKey(),
            'from_email' => $this->string(255)->notNull  (),
            'from_name' => $this->string(255),
            'subject' => $this->string(255)->notNull(),
            'template' => $this->string(60)->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'mail_log' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updated_on' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
        $this->addForeignKey('mail_queue_fk', '{{%mail_queue}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->createIndex('mail_queue_nu', '{{%mail_queue}}', 'user_id');
    }

    public function down()
    {
        // echo 'm140402_143411_create_user_module cannot be reverted.\n';
        $this->dropTable('{{%mail_queue}}');
        $this->dropTable('{{%user_ban_log}}');
        $this->dropTable('{{%user_profile}}');
        $this->dropTable('{{%remote_identity}}');
        $this->dropTable('{{%user}}');
    }
}
