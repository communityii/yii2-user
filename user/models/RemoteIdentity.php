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
 * This is the model class for table "adm_remote_identity".
 *
 * @property string $id
 * @property string $profile_id
 * @property string $provider
 * @property string $user_id
 * @property string $created_on
 * @property string $updated_on
 *
 * @property User $user
 */
class RemoteIdentity extends \yii\db\ActiveRecord
{
	/**
	 * Table name for the RemoteIdentity model
	 *
	 * @return string
	 */
	public static function tableName()
	{
		return 'adm_remote_identity';
	}

	/**
	 * RemoteIdentity model behaviors
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

	/**
	 * RemoteIdentity model validation rules
	 */
	public function rules()
	{
		return [
			[['profile_id', 'provider', 'user_id', 'created_on'], 'required'],
			[['user_id'], 'integer'],
			[['created_on', 'updated_on'], 'safe'],
			[['profile_id'], 'string', 'max' => 100],
			[['provider'], 'string', 'max' => 30],
			[['provider', 'profile_id'], 'unique', 'targetAttribute' => ['provider', 'profile_id'], 'message' => 'The combination of Profile ID and Provider has already been taken.']
		];
	}

	/**
	 * Attribute labels for the RemoteIdentity model
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('user', 'ID'),
			'profile_id' => Yii::t('user', 'Profile ID'),
			'provider' => Yii::t('user', 'Provider'),
			'user_id' => Yii::t('user', 'User ID'),
			'created_on' => Yii::t('user', 'Created On'),
			'updated_on' => Yii::t('user', 'Updated On'),
		];
	}

	/**
	 * User relation
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
