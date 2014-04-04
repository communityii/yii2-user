<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use yii\db\Schema;
use communityii\user\Module;

/**
 * Migration for creating the database structure for the communityii\yii2-user module.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class m140402_143411_create_user_module extends \yii\db\Migration
{
    // not null specification
    const NN = ' NOT NULL';

    // default timestamp
    const DT = " DEFAULT '0000-00-00 00:00:00'";

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // Table # 1: User
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_BIGPK,
            'username' => Schema::TYPE_STRING . self::NN,
            'email' => Schema::TYPE_STRING . self::NN,
            'password' => Schema::TYPE_STRING . self::NN,
            'auth_key' => Schema::TYPE_STRING . '(128)' . self::NN,
            'activation_key' => Schema::TYPE_STRING . '(128)',
            'reset_key' => Schema::TYPE_STRING . '(128)',
            'status' => Schema::TYPE_SMALLINT . self::NN . ' DEFAULT 0',
            'password_fail_attempts' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'last_login_ip' => Schema::TYPE_STRING . '(50)',
            'last_login_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
            'password_reset_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
            'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
            'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
        ], $tableOptions);
        $this->createIndex("{{%user}}_UK1", '{{%user}}', 'username', true);
        $this->createIndex("{{%user}}_UK2", '{{%user}}', 'email', true);
        $this->createIndex("{{%user}}_NU1", '{{%user}}', 'status');

        // Table # 2: Remote identity
        '{{%remote_identity}}' = '{{%remote_identity}}';
        $this->createTable('{{%remote_identity}}', [
            'id' => Schema::TYPE_BIGPK,
            'profile_id' => Schema::TYPE_STRING . '(128)' . self::NN,
            'provider' => Schema::TYPE_STRING . '(30)' . self::NN,
            'user_id' => Schema::TYPE_BIGINT . self::NN,
            'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
            'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
        ], $tableOptions);
        $this->addForeignKey("{{%remote_identity}}_FK1", '{{%remote_identity}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->createIndex("{{%remote_identity}}_FK1", '{{%remote_identity}}', 'user_id');

        // Table # 3: User profile
        $this->createTable('{{%user_profile}}', [
            'id' => Schema::TYPE_BIGINT . self::NN,
            'profile_name' => Schema::TYPE_STRING . '(180)',
            'first_name' => Schema::TYPE_STRING . "(60) DEFAULT ''",
            'last_name' => Schema::TYPE_STRING . "(60) DEFAULT ''",
            'avatar_url' => Schema::TYPE_TEXT,
            'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
            'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
        ], $tableOptions);
        $this->addPrimaryKey("{{%user_profile}}_PK1", '{{%user_profile}}', 'id');
        $this->addForeignKey("{{%user_profile}}_FK1", '{{%user_profile}}', 'id', '{{%user}}', 'id', 'CASCADE');
        $this->createIndex("{{%user_profile}}_FK1", '{{%user_profile}}', 'id');

        // Table # 4: User ban log
        $this->createTable('{{%user_ban_log}}', [
            'id' => Schema::TYPE_BIGPK,
            'user_ip' => Schema::TYPE_STRING . '(60)',
            'ban_reason' => Schema::TYPE_STRING,
            'revoke_reason' => Schema::TYPE_STRING,
            'user_id' => Schema::TYPE_BIGINT . self::NN,
            'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
            'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
        ], $tableOptions);
        $this->addForeignKey("{{%user_ban_log}}_FK1", '{{%user_ban_log}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->createIndex("{{%user_ban_log}}_FK1", '{{%user_ban_log}}', 'user_id');

        // Table # 5: Mail queue
        $this->createTable('{{%mail_queue}}', [
            'id' => Schema::TYPE_BIGPK,
            'from_email' => Schema::TYPE_STRING . self::NN,
            'from_name' => Schema::TYPE_STRING,
            'subject' => Schema::TYPE_STRING . self::NN,
            'template' => Schema::TYPE_STRING . '(60)' . self::NN,
            'user_id' => Schema::TYPE_BIGINT . self::NN,
            'mail_log' => Schema::TYPE_TEXT,
            'status' => Schema::TYPE_SMALLINT . self::NN . ' DEFAULT 0',
            'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
            'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
        ], $tableOptions);
        $this->addForeignKey("{{%mail_queue}}_FK1", '{{%mail_queue}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->createIndex("{{%mail_queue}}_FK1", '{{%mail_queue}}', 'user_id');
    }

    public function down()
    {
        // echo "m140402_143411_create_user_module cannot be reverted.\n";
        $this->dropTable('{{%mail_queue}}');
        $this->dropTable('{{%user_ban_log}}');
        $this->dropTable('{{%user_profile}}');
        $this->dropTable('{{%remote_identity}}');
        $this->dropTable('{{%user}}');
    }
}
