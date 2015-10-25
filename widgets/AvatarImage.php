<?php

namespace comyii\user\widgets;

use Yii;
use yii\helpers\Html;
use comyii\user\models\UserProfile;

class AvatarImage extends Widget
{
    /**
     * @var UserProfile the user profile model
     */
    public $profile;

    /**
     * @var array the HTML attributes for the image
     */
    public $imageOptions = [
        'style' => 'width:160px;margin-bottom:20px',
        'class' => 'img-thumbnail'
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->_module->profileSettings['enabled'] || !$this->profile) {
            echo '';
            return;
        }
        if (!isset($this->imageOptions['alt'])) {
            $this->imageOptions['alt'] = Yii::t('user', 'Avatar');
        }
        echo Html::img($this->profile->getAvatarUrl(), $this->imageOptions);
    }
}