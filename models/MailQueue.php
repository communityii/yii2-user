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
 * This is the model class for table `Module::T5`.
 *
 * @property string $id
 * @property string $from_email
 * @property string $from_name
 * @property string $subject
 * @property string $template
 * @property string $user_id
 * @property string $log
 * @property integer $status
 * @property string $created_on
 * @property string $updated_on
 *
 * @property User $user
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class MailQueue extends BaseModel
{
	const STATUS_QUEUED = 0;
	const STATUS_SENT = 1;
	const STATUS_ERROR = 2;

	/**
	 * @var array the list of statuses
	 */
	private $_statuses = [];

	/**
	 * @var array the list of status CSS classes
	 */
	private $_statusClasses = [];

	/**
	 * Table name for the Mail Queue model
	 *
	 * @return string
	 */
	public static function tableName()
	{
		return Module::T5;
	}

	/**
	 * Initialize Mail Queue model
	 */
	public function init()
	{
		parent::init();
		$this->_statuses = [
			self::STATUS_QUEUED => Yii::t('user', 'Queued'),
			self::STATUS_SENT => Yii::t('user', 'Sent'),
			self::STATUS_ERROR => Yii::t('user', 'Error')
		];
		$this->_statusClasses = [
			self::STATUS_QUEUED => 'label label-info',
			self::STATUS_SENT => 'label label-success',
			self::STATUS_ERROR => 'label label-danger'
		];
	}

	/**
	 * Mail Queue model validation rules
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['id', 'from_email', 'subject', 'template', 'user_id', 'created_on'], 'required'],
			[['id', 'user_id', 'status'], 'integer'],
			[['log'], 'string'],
			[['created_on', 'updated_on'], 'safe'],
			[['from_email', 'from_name', 'subject'], 'string', 'max' => 255],
			[['template'], 'string', 'max' => 60]
		];
	}

	/**
	 * Attribute labels for the Mail Queue model
	 *
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('user', 'ID'),
			'from_email' => Yii::t('user', 'From Email'),
			'from_name' => Yii::t('user', 'From Name'),
			'subject' => Yii::t('user', 'Subject'),
			'template' => Yii::t('user', 'Template'),
			'user_id' => Yii::t('user', 'User ID'),
			'log' => Yii::t('user', 'Log'),
			'status' => Yii::t('user', 'Status'),
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

	/**
	 * Get status list
	 *
	 * @return string
	 */
	public function getStatusList()
	{
		return $this->_statuses;
	}

	/**
	 * User friendly status name
	 *
	 * @return string
	 */
	public function getStatusText()
	{
		return $this->_statuses[$this->status];
	}

	/**
	 * Formatted status name
	 *
	 * @return string
	 */
	public function getStatusHtml()
	{
		return '<span class="' . $this->_statusClasses[$this->status] . '">' . $this->statusName . '</span>';
	}

}
