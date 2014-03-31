<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\models;

use Yii;

/**
 * This is the base model class for all active record models
 */
class BaseModel extends \yii\db\ActiveRecord
{
	/* Current module */
	protected $_module;

	public function init()
	{
		$this->_module = Yii::$app->getModule('user');
		if ($this->_module === null) {
			throw new InvalidConfigException("The module 'user' was not found. Ensure you have setup the 'user' module in your Yii configuration file.");
		}
		parent::init();
	}

	/**
	 * Model behaviors
	 */
	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_on', 'updated_on'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_on'],
				],
			],
		];
	}
}
