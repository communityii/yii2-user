<?php

namespace comyii\user\widgets;

use Yii;
use kartik\detail\DetailView;
use comyii\user\models\User;
use comyii\user\models\SocialProfile;

class SocialConnect extends Widget
{
    /**
     * @var User the user model
     */
    public $model;

    /**
     * @var SocialProfile the social profile model
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
        if (Yii::$app->user->id != $this->model->id || !$m->socialSettings['enabled']) {
            return;
        }

        $attributes = [
            [
                'group' => true,
                'label' => $m->icon('link') . Yii::t('user', 'Connect Social Accounts'),
                'rowOptions' => ['class' => 'info']
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
        ], $this->widgetOptions);

        $this->widgetOptions['model'] = $this->profile;
        $this->getView()->registerCss('.user-link-social .auth-clients {margin:4px;padding:0}');
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->user->id != $this->model->id || !$this->_module->socialSettings['enabled']) {
            echo '';
            return;
        }
        echo DetailView::widget($this->widgetOptions);
    }
}
