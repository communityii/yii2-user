<?php

namespace communityii\user\models;

use Yii;

/**
 * This is the model class for table "adm_user_profile".
 *
 * @property string $id
 * @property string $profile_name
 * @property string $first_name
 * @property string $last_name
 * @property string $avatar_url
 * @property string $created_on
 * @property string $updated_on
 *
 * @property User $user
 */
class UserProfile extends \yii\db\ActiveRecord
{
	/**
	 * Table name for the UserProfile model
	 *
	 * @return string
	 */
	public static function tableName()
	{
		return 'adm_user_profile';
	}

	/**
	 * UserProfile model behaviors
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
	 * UserProfile model validation rules
	 */
	public function rules()
	{
		return [
			[['id', 'created_on'], 'required'],
			[['id'], 'integer'],
			[['avatar_url'], 'string'],
			[['created_on', 'updated_on'], 'safe'],
			[['profile_name'], 'string', 'max' => 180],
			[['first_name', 'last_name'], 'string', 'max' => 60]
		];
	}

	/**
	 * Attribute labels for the UserProfile model
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('user', 'ID'),
			'profile_name' => Yii::t('user', 'Profile Name'),
			'first_name' => Yii::t('user', 'First Name'),
			'last_name' => Yii::t('user', 'Last Name'),
			'avatar_url' => Yii::t('user', 'Avatar Url'),
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
		return $this->hasOne(User::className(), ['id' => 'id']);
	}
}
