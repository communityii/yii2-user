<?php

namespace comyii\user\widgets;

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\Html;

class AvatarImage extends Widget
{
    public $profile;
    public $imageOptions = [];
    
    public function init() {
        parent::init();
        if(!Yii::$app->getModule('user')->profileSettings['enabled']) return;
    }
    
    public function run() {
        if(!Yii::$app->getModule('user')->profileSettings['enabled']) return;
        echo Html::img($this->profile->avatarUrl, [
            'alt' => Yii::t('user', 'Avatar'), 
            'style' => 'width:160px;margin-bottom:20px',
            'class' => 'img-thumbnail'
        ]);
    }
}