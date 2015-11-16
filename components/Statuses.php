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

use yii\base\Component;
use comyii\user\Module;

/**
 * Class Models the status settings for the module.
 * 
 * @package comyii\user\components
 */
class Statuses extends Component
{
    public $statuses = [
        Module::MODEL_LOGIN => 'comyii\user\models\LoginForm',
        Module::MODEL_USER => 'comyii\user\models\User',
        Module::MODEL_USER_SEARCH => 'comyii\user\models\UserSearch',
        Module::MODEL_PROFILE => 'comyii\user\models\UserProfile',
        Module::MODEL_SOCIAL_PROFILE => 'comyii\user\models\SocialProfile',
        Module::MODEL_PROFILE_SEARCH => 'comyii\user\models\UserProfileSearch',
        Module::MODEL_RECOVERY => 'comyii\user\models\RecoveryForm',
    ];

    /**
     * Constructor for Statuses component
     * 
     * @param type $config
     */
    public function __construct($config = array(), $m = null) {
        if ($m === null) {
            $m = Yii::$app->getModule('user');
        }
        $this->statuses = [
            Module::STATUS_SUPERUSER => Yii::t('user', 'Superuser'),
            Module::STATUS_PENDING => Yii::t('user', 'Pending'),
            Module::STATUS_ACTIVE => Yii::t('user', 'Active'),
            Module::STATUS_INACTIVE => Yii::t('user', 'Inactive'),
            Module::STATUS_ADMIN => Yii::t('user', 'Admin'),
            Module::STATUS_LOCKED => Yii::t('user', 'Locked'),
            Module::STATUS_EXPIRED => Yii::t('user', 'Expired'),
        ];
        if ($m->userTypes[$m->getUserType()]['models']) {
            $config = Module::mergeDefault($config, $m->userTypes[$m->getUserType()]['statuses']);
        }
        if (isset($config['statuses'])) {
            $this->statuses = array_replace_recursive($this->statuses, $config['statuses']);
        }
    }

    /**
     * Get edit status list
     *
     * @return string
     */
    public function getValidStatuses()
    {
        $statuses = [];
        $exclude1 = array_flip($this->secondaryStatuses);
        $exclude2 = array_flip($this->internalStatuses);
        foreach ($this->statuses as $status => $name) {
            if (!isset($exclude1[$status]) && !isset($exclude2[$status])) {
                $statuses[$status] = $name;
            }
        }
        return $statuses;
    }

    /**
     * Get edit status list
     *
     * @return string
     */
    public function getEditStatuses()
    {
        $statuses = [];
        $exclude = array_flip($this->secondaryStatuses);
        foreach ($this->statuses as $status => $name) {
            if (!isset($exclude[$status])) {
                $statuses[$status] = $name;
            }
        }
        return $statuses;
    }

    /**
     * Get disabled statuses
     *
     * @return array
     */
    public function getDisabledStatuses()
    {
        $options = [];
        foreach ($this->internalStatuses as $status) {
            $options[$status] = ['disabled' => true];
        }
        return $options;
    }

    /**
     * Get primary status list
     *
     * @return string
     */
    public function getPrimaryStatuses()
    {
        $statuses = [];
        $exclude = array_flip($this->secondaryStatuses);
        foreach ($this->statuses as $status => $name) {
            if (!isset($exclude[$status])) {
                $statuses[$status] = $name;
            }
        }
        return $statuses;
    }

    /**
     * Get secondary status list
     *
     * @return string
     */
    public function getSecondaryStatuses()
    {
        $statuses = [];
        foreach ($this->secondaryStatuses as $status) {
            $statuses[$status] = $this->statuses[$status];
        }
        return $statuses;
    }

}

