<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\components;

use Yii;
use yii\base\Component;

/**
 * Class Notification the notification settings for the module.
 * 
 * @package comyii\user\components
 */
class Notification extends Component
{
    /**
     * @var string the default mail from address. Can be overwritten in each notification. 
     * Defaults to `Yii::$app->params['supportEmail']`
     */
    public $fromMail;
    
    /**
     * @var string the default mail from address. Can be overwritten in each notification. 
     * Defaults to `Yii::$app->name`
     */
    public $fromName;
    
    /**
     * @var string the path for notification email templates
     */
    public $viewPath = '@vendor/communityii/yii2-user/views/mail';
    
    /**
     * @var array the settings for the activation notification
     */
    public $activation;
    
    /**
     * @var array the settings for the recovery notification
     */
    public $recovery;
    
    /**
     * @var array the settings for the email change notification
     */
    public $newemail;
    
    /**
     * @var array the settings for the welcome notification
     */
    public $welcome;
    
    public function __construct($config = array()) {
        $this->fromMail = Yii::$app->params['supportEmail'];
        $this->fromName = Yii::$app->name;
        parent::__construct($config);
    }
    
    public function getMailFrom()
    {
        if ($this->mailFrom === null) {
            $this->mailFrom = Yii::$app->params['supportEmail'];
        }
        return $this->mailFrom;
    }
    
    public function getActivation()
    {
        if ($this->activation === null) {
            $this->activation = [
                'fromEmail' => $this->fromMail,
                'fromName' => $this->fromName,
                'subject' => Yii::t('user', Yii::t('user', 'Account activation for {appname}', ['appname' => Yii::$app->name]))
            ];
        }
    }
    
    public function getRecovery()
    {
        if ($this->activation === null) {
            $this->activation = [
                'fromEmail' => $this->fromMail,
                'fromName' => $this->fromName,
                'subject' => Yii::t('user', Yii::t('user', 'Account recovery for {appname}', ['appname' => Yii::$app->name]))
            ];
        }
    }
    
    public function getNewemail()
    {
        if ($this->activation === null) {
            $this->activation = [
                'fromEmail' => $this->fromMail,
                'fromName' => $this->fromName,
                'subject' => Yii::t('user', Yii::t('user', 'Account new email for {appname}', ['appname' => Yii::$app->name]))
            ];
        }
    }
    
    public function getWelcome()
    {
        if ($this->activation === null) {
            $this->activation = [
                'fromEmail' => $this->fromMail,
                'fromName' => $this->fromName,
                'subject' => Yii::t('user', Yii::t('user', 'Welcome to {appname}', ['appname' => Yii::$app->name]))
            ];
        }
    }
}
