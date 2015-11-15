<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */
namespace comyii\user\models;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table {{%user_profile}}.
 *
 * @property string  $id
 * @property string  $first_name
 * @property string  $last_name
 * @property string  $gender
 * @property string  $avatar
 * @property integer $created_on
 * @property integer $updated_on
 * @property string  genderText
 * @property string  avatarFile
 * @property User    $user
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class UserProfile extends BaseModel
{
    const G_MALE = 'M';
    const G_FEMALE = 'F';
    const G_OTHER = 'O';

    /**
     * @var UploadedFile the image file blob uploaded
     */
    public $image;

    /**
     * @var array the list of genders
     */
    private $_genders = [];

    /**
     * @var array the list of gender CSS classes
     */
    private $_genderClasses = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_genders = [
            self::G_MALE => Yii::t('user', 'Male'),
            self::G_FEMALE => Yii::t('user', 'Female'),
            self::G_OTHER => Yii::t('user', 'Other'),
        ];
        $this->_genderClasses = [
            self::G_MALE => 'text-info',
            self::G_FEMALE => 'text-danger',
            self::G_OTHER => 'text-muted'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['avatar'], 'string'],
            [['first_name', 'last_name', 'gender', 'birth_date', 'image'], 'safe'],
            [['birth_date'], 'date', 'format' => 'php:Y-m-d'],
            [['gender'], 'string', 'max' => 5],
            [['first_name', 'last_name'], 'string', 'max' => 60]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'first_name' => Yii::t('user', 'First Name'),
            'last_name' => Yii::t('user', 'Last Name'),
            'gender' => Yii::t('user', 'Gender'),
            'birth_date' => Yii::t('user', 'Birth Date'),
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
     * Get gender list
     *
     * @return string
     */
    public function getGenderList()
    {
        return $this->_genders;
    }

    /**
     * User friendly gender name
     *
     * @return string
     */
    public function getGenderText()
    {
        if (empty($this->gender)) {
            return null;
        }
        return $this->_genders[$this->gender];
    }

    /**
     * Formatted gender name
     *
     * @return string
     */
    public function getGenderHtml()
    {
        if (empty($this->gender)) {
            return null;
        }
        return '<span class="' . $this->_genderClasses[$this->gender] . '">' . $this->genderText . '</span>';
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
        $file = $filename = null;
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
