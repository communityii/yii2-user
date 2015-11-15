<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\authclient;

use yii\authclient\AuthAction as Action;

class AuthAction extends Action
{
    
    /**
     * @var string the user type, must be defined in module configuration.
     */
    public $userType;
    
    /**
     * @var string the redirect url after successful authorization.
     */
    private $_successUrl = '';
    /**
     * @var string the redirect url after unsuccessful authorization (e.g. user canceled).
     */
    private $_cancelUrl = '';
    
    /**
     * Runs the action.
     */
    public function run()
    {
        if(isset($_GET['userType']) && $this->controller->module->isUserType($_GET['userType'])) {
            $this->userType = $_GET['userType'];
        } elseif(isset($_GET['userType'])) {
            throw new \yii\base\Exception('Invalid user type specified');
        }
        parent::run();
    }
    
    /**
     * @return string successful URL.
     */
    public function getSuccessUrl()
    {
        if (empty($this->_successUrl)) {
            $this->_successUrl = $this->controller->module->getSocialSetting('defaultSuccessUrl', $this->userType, $this->defaultSuccessUrl());
        }
        return parent::getSuccessUrl();
    }
    
    /**
     * @return string cancel URL.
     */
    public function getCancelUrl()
    {
        if (empty($this->_cancelUrl)) {
            $this->_cancelUrl = $this->controller->module->getSocialSetting('defaultCancelUrl', $this->userType, $this->defaultCancelUrl());
        }
        return $this->_cancelUrl;
    }
}