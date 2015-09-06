<?php

namespace comyii\user\models;

use Yii;

/**
 * This is the model class for table "{{%social_auth}}".
 *
 * @property string $id
 * @property string $source
 * @property string $source_id
 * @property string $user_id
 * @property string $created_on
 * @property string $updated_on
 *
 * @property User $user
 */
class SocialProfile extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%social_auth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'source_id'], 'required'],
            [['user_id'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['source', 'source_id'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'source' => Yii::t('app', 'Source'),
            'source_id' => Yii::t('app', 'Source ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_on' => Yii::t('app', 'Updated On'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
