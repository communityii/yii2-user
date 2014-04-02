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
		if (($module = Yii::$app->getModule('user')) === null) {
			echo "\nThe module 'user' was not found . Ensure you have setup the 'user' module in your Yii configuration file.";
			return false;
		}
		extract($module->tableSettings);

		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		// Table # 1: User
		$this->createTable($t1, [
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
		$this->createIndex("{$t1}_UK1", $t1, 'username', true);
		$this->createIndex("{$t1}_UK2", $t1, 'email', true);
		$this->createIndex("{$t1}_NU1", $t1, 'status');

		// Table # 2: Remote identity
		$this->createTable($t2, [
			'id' => Schema::TYPE_BIGPK,
			'profile_id' => Schema::TYPE_STRING . '(128)' . self::NN,
			'provider' => Schema::TYPE_STRING . '(30)' . self::NN,
			'user_id' => Schema::TYPE_BIGINT . self::NN,
			'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
			'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
		], $tableOptions);
		$this->addForeignKey("{$t2}_FK1", $t2, 'user_id', $t1, 'id', 'CASCADE');
		$this->createIndex("{$t2}_FK1", $t2, 'user_id');

		// Table # 3: User profile
		$this->createTable($t3, [
			'id' => Schema::TYPE_BIGINT . self::NN,
			'profile_name' => Schema::TYPE_STRING . '(180)',
			'first_name' => Schema::TYPE_STRING . "(60) DEFAULT ''",
			'last_name' => Schema::TYPE_STRING . "(60) DEFAULT ''",
			'avatar_url' => Schema::TYPE_TEXT,
			'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
			'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
		], $tableOptions);
		$this->addPrimaryKey("{$t3}_PK1", $t3, 'id');
		$this->addForeignKey("{$t3}_FK1", $t3, 'id', $t1, 'id', 'CASCADE');
		$this->createIndex("{$t3}_FK1", $t3, 'id');

		// Table # 4: User ban log
		$this->createTable($t4, [
			'id' => Schema::TYPE_BIGPK,
			'user_ip' => Schema::TYPE_STRING . '(60)',
			'ban_reason' => Schema::TYPE_STRING,
			'revoke_reason' => Schema::TYPE_STRING,
			'user_id' => Schema::TYPE_BIGINT . self::NN,
			'created_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
			'updated_on' => Schema::TYPE_TIMESTAMP . self::NN . self::DT,
		], $tableOptions);
		$this->addForeignKey("{$t4}_FK1", $t4, 'user_id', $t1, 'id', 'CASCADE');
		$this->createIndex("{$t4}_FK1", $t4, 'user_id');

		// Table # 5: Mail queue
		$this->createTable($t5, [
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
		$this->addForeignKey("{$t5}_FK1", $t5, 'user_id', $t1, 'id', 'CASCADE');
		$this->createIndex("{$t5}_FK1", $t5, 'user_id');
	}

	public function down()
	{
		// echo "m140402_143411_create_user_module cannot be reverted.\n";
		if (($module = Yii::$app->getModule('user')) === null) {
			echo "\nThe module 'user' was not found . Ensure you have setup the 'user' module in your Yii configuration file.";
			return false;
		}
		extract($module->tableSettings);
		$this->dropTable($t5);
		$this->dropTable($t4);
		$this->dropTable($t3);
		$this->dropTable($t2);
		$this->dropTable($t1);
	}
}
