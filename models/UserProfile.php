<?php

namespace comyii\user\models;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table {{%user_profile}}.
 *
 * @property string $id
 * @property string $display_name
 * @property string $first_name
 * @property string $last_name
 * @property string $avatar
 * @property string $created_on
 * @property string $updated_on
 *
 * @property User $user
 */
class UserProfile extends BaseModel
{
    /**
     * @var UploadedFile the image file blob uploaded
     */
    public $image;
    
    /**
     * Table name for the UserProfile model
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * UserProfile model validation rules
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['avatar'], 'string'],
            [['first_name', 'last_name', 'display_name', 'image'], 'safe'],
            [['display_name'], 'string', 'max' => 180],
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
            'display_name' => Yii::t('user', 'Display Name'),
            'first_name' => Yii::t('user', 'First Name'),
            'last_name' => Yii::t('user', 'Last Name'),
            'avatar' => Yii::t('user', 'Avatar'),
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
     * Fetch stored avatar file name with complete path
     *
     * @return string
     */
    public function getAvatarFile()
    {
        $config = $this->_module->profileSettings;
        return isset($this->avatar) ? Yii::getAlias($config['basePath']) . '/' . $this->avatar : null;
    }

    /**
     * Fetch stored avatar url
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        $config = $this->_module->profileSettings;
        $file = isset($this->avatar) && file_exists($this->avatarFile) ? $this->avatar : $config['defaultAvatar'];
        return empty($file) ? '' : Url::to($config['baseUrl'] . '/' . $file, true);
    }

    /**
     * Process upload of avatar
     *
     * @return bool the status of the upload
     */
    public function uploadAvatar()
    {
        $image = UploadedFile::getInstance($this, 'image');
        if (empty($image)) {
            return false;
        }
        $ext = $image->extension;
        $file = null;
        $config = $this->_module->profileSettings;
        while ($file === null || file_exists($file)) {
            $filename = Yii::$app->security->generateRandomString(8) . ".{$ext}";
            $file = Yii::getAlias($config['basePath']) . '/' . $filename;
        }
        if ($image->saveAs($file)) {
            $this->deleteAvatar();
            $this->avatar = $filename;
            return true;
        }
        return false;
    }

    /**
     * Process deletion of avatar
     *
     * @return bool the status of deletion
     */
    public function deleteAvatar()
    {
        $file = $this->getAvatarFile();
        if (empty($file) || !file_exists($file)) {
            return false;
        }
        if (!unlink($file)) {
            return false;
        }
        $this->avatar = null;
        return true;
    }    
}
