<?php

namespace comyii\user\widgets;

use Yii;
use comyii\user\Module;

class Widget extends \yii\bootstrap\Widget
{
    /**
     * @var Module the user module instance
     */
    protected $_module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initModule();
        parent::init();
    }

    /**
     * Initializes the module variable
     */
    protected function initModule()
    {
        $this->_module = Yii::$app->getModule('user');
    }
}
