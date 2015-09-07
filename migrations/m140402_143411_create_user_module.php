<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */
use yii\db\Migration;

/**
 * Migration for creating the database structure for the comyii\yii2-user module.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class m140402_143411_create_user_module extends Migration
{
    public function up()
    {
        $tableOptions = null;
        $timestamp = $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00');

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // Table # 1: User
        $this->createTable('{{%user}}', [
            'id' => $this->bigPrimaryKey(),
            'username' => $this->string(30)->notNull()->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'email_new' => $this->string(255),
            'password_hash' => $this->string(128)->notNull(),
            'auth_key' => $this->string(128)->notNull(),
            'activation_key' => $this->string(128),
            'reset_key' => $this->string(128),
            'email_change_key' => $this->string(128),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'password_fail_attempts' => $this->smallInteger()->defaultValue(0),
            'password_reset_on' => $this->timestamp(),
            'created_on' => $timestamp,
            'updated_on' => $timestamp,
            'last_login_on' => $this->timestamp(),
            'last_login_ip' => $this->string(50)
        ], $tableOptions);
        $this->createIndex('user_status_idx', '{{%user}}', 'status');

        // Table # 2: Social authentication
        $this->createTable('{{%social_auth}}', [
            'id' => $this->bigPrimaryKey(),
            'source' => $this->string(255)->notNull(),
            'source_id' => $this->string(255)->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'created_on' => $timestamp,
            'updated_on' => $timestamp,
        ], $tableOptions);
        $this->addForeignKey(
            'social_auth_user_id_fk',
            '{{%social_auth}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
        $this->createIndex('social_auth_user_id_idx', '{{%social_auth}}', 'user_id');

        // Table # 3: User profile
        $this->createTable('{{%user_profile}}', [
            'id' => $this->bigInteger()->notNull(),
            'display_name' => $this->string(120),
            'first_name' => $this->string(60),
            'last_name' => $this->string(60),
            'avatar' => $this->string(150),
            'created_on' => $timestamp,
            'updated_on' => $timestamp,
        ], $tableOptions);
        $this->addPrimaryKey('user_profile_user_id_pk', '{{%user_profile}}', 'id');
        $this->addForeignKey(
            'user_profile_user_id_fk',
            '{{%user_profile}}',
            'id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // Table # 4: Mail queue
        $this->createTable('{{%mail_queue}}', [
            'id' => $this->bigPrimaryKey(),
            'from_email' => $this->string(255)->notNull(),
            'from_name' => $this->string(255),
            'subject' => $this->string(255)->notNull(),
            'template' => $this->string(60)->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'mail_log' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_on' => $timestamp,
            'updated_on' => $timestamp,
        ], $tableOptions);
        $this->addForeignKey('mail_queue_user_id_fk', '{{%mail_queue}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->createIndex('mail_queue_user_id_idx', '{{%mail_queue}}', 'user_id');
    }

    public function down()
    {
        // echo 'm140402_143411_create_user_module cannot be reverted.\n';
        $this->dropTable('{{%mail_queue}}');
        $this->dropTable('{{%user_profile}}');
        $this->dropTable('{{%social_auth}}');
        $this->dropTable('{{%user}}');
    }
}
