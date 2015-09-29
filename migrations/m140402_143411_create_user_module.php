<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
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
        $timestamp = $this->integer()->notNull();

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
            'status_sec' => $this->smallInteger(),
            'password_fail_attempts' => $this->smallInteger()->defaultValue(0),
            'password_reset_on' => $this->integer(),
            'created_on' => $timestamp,
            'updated_on' => $timestamp,
            'last_login_on' => $this->integer(),
            'last_login_ip' => $this->string(50)
        ], $tableOptions);
        $this->createIndex('user_status_idx', '{{%user}}', 'status');
        $this->createIndex('user_status_sec_idx', '{{%user}}', 'status_sec');

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
            'first_name' => $this->string(60),
            'last_name' => $this->string(60),
            'gender' => $this->string(1)->defaultValue('M'),
            'birth_date' => $this->date(),
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
    }

    public function down()
    {
        $this->dropTable('{{%user_profile}}');
        $this->dropTable('{{%social_auth}}');
        $this->dropTable('{{%user}}');
    }
}
