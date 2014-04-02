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

	const NN = ' NOT NULL'; // not null specification
	const DT = " DEFAULT '0000-00-00 00:00:00'"; // default timestamp

	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		// Table # 1: User
		$this->createTable(Module::T1, [
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
		$this->createIndex(Module::T1 . '_UK1', Module::T1, 'username', true);
		$this->createIndex(Module::T1 . '_UK2', Module::T1, 'email', true);
		$this->createIndex(Module::T1 . '_NU1', Module::T1, 'status');

		// Table # 2: Remote identity
		$this->createTable(Module::T2, [
			'id' => Schema::TYPE_BIGPK,
			'profile_id' => Schema::TYPE_STRING . '(128)' . self::NN,
			'provider' => Schema::TYPE_STRING . '(30)' . self::NN,
			'user_id' => Schema::TYPE_BIGINT . self::NN,
			'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
			'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
		], $tableOptions);
		$this->addForeignKey(Module::T2 . '_FK1', Module::T2, 'user_id', Module::T1, 'id', 'CASCADE');
		$this->createIndex(Module::T2 . '_FK1', Module::T2, 'user_id');

		// Table # 3: User profile
		$this->createTable(Module::T3, [
			'id' => Schema::TYPE_BIGINT . self::NN,
			'profile_name' => Schema::TYPE_STRING . '(180)',
			'first_name' => Schema::TYPE_STRING . "(60) DEFAULT ''",
			'last_name' => Schema::TYPE_STRING . "(60) DEFAULT ''",
			'avatar_url' => Schema::TYPE_TEXT,
			'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
			'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
		], $tableOptions);
		$this->addPrimaryKey(Module::T3 . '_PK1', Module::T3, 'id');
		$this->addForeignKey(Module::T3 . '_FK1', Module::T3, 'id', Module::T1, 'id', 'CASCADE');
		$this->createIndex(Module::T3 . '_FK1', Module::T3, 'id');

		// Table # 4: User ban log
		$this->createTable(Module::T4, [
			'id' => Schema::TYPE_BIGPK,
			'user_ip' => Schema::TYPE_STRING . '(60)',
			'ban_reason' => Schema::TYPE_STRING,
			'revoke_reason' => Schema::TYPE_STRING,
			'user_id' => Schema::TYPE_BIGINT . self::NN,
			'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
			'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
		], $tableOptions);
		$this->addForeignKey(Module::T4 . '_FK1', Module::T4, 'user_id', Module::T1, 'id', 'CASCADE');
		$this->createIndex(Module::T4 . '_FK1', Module::T4, 'user_id');

		// Table # 5: Mail queue
		$this->createTable(Module::T5, [
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
		$this->addForeignKey(Module::T5 . '_FK1', Module::T5, 'user_id', Module::T1, 'id', 'CASCADE');
		$this->createIndex(Module::T5 . '_FK1', Module::T5, 'user_id');
	}

	public function down()
	{
		// echo "m140402_143411_create_user_module cannot be reverted.\n";
		$this->dropTable(Module::T5);
		$this->dropTable(Module::T4);
		$this->dropTable(Module::T3);
		$this->dropTable(Module::T2);
		$this->dropTable(Module::T1);
	}
}
