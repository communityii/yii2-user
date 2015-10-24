<?php

namespace comyii\user\widgets;

use Yii;
use yii\bootstrap\Widget;
use kartik\detail\DetailView;

class ProfileDetails extends Widget
{
    public $profile;
    public $attributes = [];
    public $widgetOptions = [];
    
    
    public function init() {
        parent::init();
        $m = Yii::$app->getModule('user');
        if(!$m->profileSettings['enabled']) return;
        
        // basic attributes from Profile model
        $attributes = [
            [
                'group' => true,
                'label' => $m->icon('user') . Yii::t('user', 'Profile Details'),
                'rowOptions' => ['class'=>'info']
            ],
            'first_name',
            'last_name',
            [
                'attribute' => 'gender',
                'format' => 'raw',
                'value' => $this->profile->genderHtml,
                'labelColOptions' => ['style' => 'width:40%;text-align:right']
            ],
            'birth_date:date'
        ];
        
        $this->attributes = array_replace_recursive($attributes, $this->attributes);
        
        $this->widgetOptions = array_replace_recursive([
            'striped' => false,
            'hover' => true, 
            'enableEditMode' => false,
            'attributes' => $this->attributes
        ],$this->widgetOptions);
        
        $this->widgetOptions['model'] = $this->profile;
    }
    
    public function run() {
        if(!Yii::$app->getModule('user')->profileSettings['enabled']) return;
        echo DetailView::widget($this->widgetOptions);
    }
}