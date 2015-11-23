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
use comyii\user\components\ArrayComponent;

/**
 * Class Models the status settings for the module.
 * 
 * @package comyii\user\components
 */
class Statuses extends ArrayComponent
{
    const STATUS_SUPERUSER = -1;
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_ADMIN = 3;
    const STATUS_EXPIRED = 4;
    const STATUS_LOCKED = 5;

    /**
     * @var string the name of the property to store the array items 
     */
    protected $_containerName = 'statuses';
    protected $_currentName = 'status';

    /**
     * @var array the list of layouts 
     */
    public $statuses;

    public $status;

    /**
     * @var array the user statuses which are internal to system and not
     * available for direct update by admin or superuser
     */
    protected $internal = [
        self::STATUS_SUPERUSER,
        self::STATUS_PENDING
    ];

    /**
     * @var array the user statuses which are secondary
     */
    protected $secondary = [
        self::STATUS_LOCKED,
        self::STATUS_EXPIRED,
    ];

    /**
     * @var array the CSS classes for displaying user status as HTML
     */
    public $classes = [
        self::STATUS_SUPERUSER => 'label label-primary',
        self::STATUS_PENDING => 'label label-warning',
        self::STATUS_ACTIVE => 'label label-success',
        self::STATUS_INACTIVE => 'label label-danger',
        self::STATUS_ADMIN => 'label label-info',
        self::STATUS_EXPIRED => 'label label-default',
        self::STATUS_LOCKED => 'label label-danger',
    ];

    /**
     * Get the default actions
     * 
     * @return array
     */
    public function getDefaults()
    {
        return [
            self::STATUS_SUPERUSER => Yii::t('user', 'Superuser'),
            self::STATUS_PENDING => Yii::t('user', 'Pending'),
            self::STATUS_ACTIVE => Yii::t('user', 'Active'),
            self::STATUS_INACTIVE => Yii::t('user', 'Inactive'),
            self::STATUS_ADMIN => Yii::t('user', 'Admin'),
            self::STATUS_LOCKED => Yii::t('user', 'Locked'),
            self::STATUS_EXPIRED => Yii::t('user', 'Expired'),
        ];
    }

    /**
     * Get edit status list
     *
     * @return string
     */
    public function getValid()
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
    public function getEdit()
    {
        $statuses = [];
        $exclude = array_flip($this->secondary);
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
    public function getDisabled()
    {
        $options = [];
        foreach ($this->internal as $status) {
            $options[$status] = ['disabled' => true];
        }
        return $options;
    }

    /**
     * Get primary status list
     *
     * @return string
     */
    public function getPrimary()
    {
        $statuses = [];
        $exclude = array_flip($this->secondary);
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
    public function getSecondary()
    {
        $statuses = [];
        foreach ($this->secondary as $status) {
            $statuses[$status] = $this->statuses[$status];
        }
        return $statuses;
    }

}

