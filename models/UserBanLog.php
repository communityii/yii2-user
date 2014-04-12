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
 * This is the model class for table {{%user_ban_log}}.
 *
 * @property string $id
 * @property string $user_ip
 * @property string $ban_reason
 * @property string $revoke_reason
 * @property string $user_id
 * @property string $banned_till
 * @property string $created_on
 * @property string $updated_on
 *
 * @property User $user
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class UserBanLog extends BaseModel
{
    /**
     * Table name for the User Ban Log model
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%user_ban_log}}';
    }

    /**
     * User Ban Log model validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'created_on'], 'required'],
            [['id', 'user_id'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['user_ip'], 'string', 'max' => 50],
            [['ban_reason', 'revoke_reason'], 'string', 'max' => 255]
        ];
    }

    /**
     * Attribute labels for the User Ban Log model
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'user_ip' => Yii::t('user', 'User Ip'),
            'ban_reason' => Yii::t('user', 'Ban Reason'),
            'revoke_reason' => Yii::t('user', 'Revoke Reason'),
            'user_id' => Yii::t('user', 'User ID'),
            'banned_till' => Yii::t('user', 'Banned Till'),
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
