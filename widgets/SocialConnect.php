<?php

namespace comyii\user\widgets;

use Yii;
use yii\bootstrap\Widget;
use kartik\detail\DetailView;

class SocialConnect extends Widget
{
    public $model;
    public $profile;
    public $attributes = [];
    public $widgetOptions = [];
    
    
    public function init() {
        parent::init();
        $m = Yii::$app->getModule('user');
        if (Yii::$app->user->id != $this->model->id || !$m->socialSettings['enabled']) return;
        
        $attributes = [
            [
                'group' => true,
                'label' => $m->icon('link') . Yii::t('user', 'Connect Social Accounts'),
                'rowOptions' => ['class'=>'info']
            ],
            [
                'group' => true,
                'label' => $m->getSocialWidget()
            ]
        ];
        
        $this->attributes = array_replace_recursive($attributes, $this->attributes);
        
        $this->widgetOptions = array_replace_recursive([
            'striped' => false,
            'enableEditMode' => false,
            'attributes' => $this->attributes
        ],$this->widgetOptions);
        
        $this->widgetOptions['model'] = $this->profile;
        $this->getView()->registerCss('.user-link-social .auth-clients {margin:4px;padding:0}');
    }
    
    public function run() {
        if (Yii::$app->user->id != $this->model->id || !Yii::$app->getModule('user')->socialSettings['enabled']) return;
        echo DetailView::widget($this->widgetOptions);
    }
}