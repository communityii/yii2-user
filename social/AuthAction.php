<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\social;

use Yii;
use comyii\user\Module;
use yii\helpers\ArrayHelper;
use yii\base\InvalidParamException;

class AuthAction extends \yii\authclient\AuthAction
{
    /**
     * @var string the user type, must be defined in module configuration.
     */
    public $userType;

    /**
     * @var Module the current user module
     */
    protected $_module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_module = Yii::$app->getModule('user');
    }

    /**
     * @inheritdoc
     * @throws InvalidParamException
     */
    public function run()
    {
        $userType = ArrayHelper::getValue($_GET, 'userType', null);
        if (!$userType) {
            parent::run();
            return;
        }
        if ($this->_module->isUserType($userType)) {
            $this->userType = $userType;
            parent::run();
        } else {
            throw new InvalidParamException("Invalid user type '{$userType}.'");
        }
    }

    /**
     * @inheritdoc
     */
    public function getSuccessUrl()
    {
        return $this->_module->getSocialSetting('defaultSuccessUrl', $this->userType, parent::getSuccessUrl());
    }

    /**
     * @inheritdoc
     */
    public function getCancelUrl()
    {
        return $this->_module->getSocialSetting('defaultCancelUrl', $this->userType, parent::getCancelUrl());
    }
}
