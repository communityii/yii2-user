<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\components;

use Yii;
use yii\base\Component;

/**
 * Class Profile the user profile component for the module.
 * 
 * @package comyii\user\components
 */
class Profile extends Component
{
    /**
     * @var bool whether the user profile is enabled for the module. Defaults to `true`.
     */
    public $enabled = true;
    
    /**
     * @var string the default file path where uploads will be stored. You can use Yii path
     * aliases for setting this. Defaults to '@webroot/uploads'.
     */
    public $basePath = '@webroot/uploads';
    
    /**
     * @var string the absolute baseUrl pointing to the uploads path. Defaults to '/uploads'.
     * You must set the full absolute url here to enable avatar URL to be parsed seamlessly across
     * both frontend and backend apps in yii2-app-advanced.
     */
    public $baseUrl = '/uploads';
    
    /**
     * @var string the filename for the default avatar located in the above path which will
     * be displayed when no profile image file is found. Defaults to `avatar.png`.
     */
    public $defaultAvatar = 'avatar.png';
    
    /**
     * @var array|bool the widget settings for FileInput widget to upload the avatar.
     * If this is set to `false`, no avatar / image upload will be enabled for the user.
     */
    private $avatar;
    
    /**
     * Get the Avatar widget
     * @return \yii\base\Widget
     */
    public function getAvatar()
    {
        if ($this->avatar === null) {
            $this->avatar = [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'elErrorContainer' => '#user-avatar-errors',
                    'allowedFileExtensions' => ['jpg', 'gif', 'png'],
                    'maxFileSize' => 200,
                    'showCaption' => false,
                    'overwriteInitial' => true,
                    'browseLabel' => '',
                    'removeLabel' => '',
                    'removeIcon' => '<i class="glyphicon glyphicon-ban-circle"></i>',
                    'browseIcon' => '<i class="glyphicon glyphicon-folder-open"></i>',
                    'showClose' => false,
                    'showUpload' => false,
                    'removeTitle' => Yii::t('user', 'Cancel or reset changes'),
                    'msgErrorClass' => 'alert alert-block alert-danger',
                    'previewSettings' => [
                        'image' => ['width' => 'auto', 'height' => '180px'],
                    ]
                ]
            ];
        }
        if (!($this->avatar) instanceof \yii\base\Widget) {
            $this->avatar = Yii::createObject($this->avatar['class'], $this->avatar);
        }
        return $this->avatar;
    }
    
}
