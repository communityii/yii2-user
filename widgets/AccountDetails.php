<?php

namespace comyii\user\widgets;

use Yii;
use yii\bootstrap\Widget;
use kartik\detail\DetailView;

class AccountDetails extends Widget
{
    public $model;
    public $attributes = [];
    public $widgetOptions = [];
    
    public function init() {
        parent::init();
        $m = Yii::$app->getModule('user');
        $editSettings = $m->getEditSettingsUser($this->model);
        // basic attributes from User model
        $attributes = [
            [
                'group'=>true,
                'label'=> $m->icon('tag') . Yii::t('user', 'Account Details'),
                'rowOptions'=>['class'=>'info']
            ],
            'username',
            'email:email',
        ];
        
        if ($editSettings['changeEmail'] && !empty($this->model->email_new)) {
            $attributes[] = 'email_new:email';
        }
        
        $attributes[] = [
            'attribute' => 'created_on',
            'label' => Yii::t('user', 'Registered On'),
            'format'=>['datetime', $m->datetimeDispFormat],
            'labelColOptions' => ['style' => 'width:40%;text-align:right']
        ];
        $attributes[] = [
            'attribute' => 'last_login_on',
            'format'=>['datetime', $m->datetimeDispFormat],
            'value' => strtotime($this->model->last_login_on) ? $this->model->last_login_on : null,
        ];
        $this->attributes = array_replace_recursive($attributes, $this->attributes);
        $this->widgetOptions = array_replace_recursive([
            'striped' => false,
            'hover' => true, 
            'enableEditMode' => false,
            'attributes' => $this->attributes
        ],$this->widgetOptions);
        
        $this->widgetOptions['model'] = $this->model;
    }
    
    public function run() {
        echo DetailView::widget($this->widgetOptions);
    }
}