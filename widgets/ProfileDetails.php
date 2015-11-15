<?php

namespace comyii\user\widgets;

use Yii;
use kartik\detail\DetailView;
use comyii\user\models\UserProfile;

class ProfileDetails extends Widget
{
    /**
     * @var UserProfile the user profile model
     */
    public $profile;

    /**
     * @var array the attributes configuration that will be used by DetailView
     */
    public $attributes = [];

    /**
     * @var array the widget configuration options for DetailView
     */
    public $widgetOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $m = $this->_module;
        if (!$m->profileSettings['enabled']) {
            return;
        }
        // basic attributes from Profile model
        $attributes = [
            [
                'group' => true,
                'label' => $m->icon('user') . Yii::t('user', 'Profile Details'),
                'rowOptions' => ['class' => 'info']
            ],
            'first_name',
            'last_name',
            [
                'attribute' => 'gender',
                'format' => 'raw',
                'value' => $this->profile->getGenderHtml(),
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
        ], $this->widgetOptions);
        $this->widgetOptions['model'] = $this->profile;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->_module->profileSettings['enabled']) {
            return;
        }
        echo DetailView::widget($this->widgetOptions);
    }
}
