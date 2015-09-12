<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\components;

use Yii;

/**
 * User authentication component
 *
 * @property string                   $username
 * @property \comyii\user\models\User $identity
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class User extends \yii\web\User
{
    /**
     * @var boolean whether to enable cookie-based login. Defaults to true.
     */
    public $enableAutoLogin = true;

    /**
     * @var string|array the URL for login when [[loginRequired()]] is called.
     */
    public $loginUrl = ['/user/account/login'];

    /**
     * Initializes the User component
     */
    public function init()
    {
        if ($this->identityClass == null) {
            $this->identityClass = 'comyii\user\models\User';
        }
        parent::init();
    }

    /**
     * Gets the user name
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->identity ? $this->identity->username : false;
    }

    /**
     * Gets the user email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->identity ? $this->identity->email : false;
    }

    /**
     * Is the user an admin
     *
     * @return bool
     */
    public function getIsSuperuser()
    {
        return $this->identity ? $this->identity->isSuperuser() : false;
    }

    /**
     * Is the user an admin
     *
     * @return bool
     */
    public function getIsAdmin()
    {
        return $this->identity ? $this->identity->isAdmin() : false;
    }

    /**
     * Is the user account active
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->identity ? $this->identity->isActive() : false;
    }
}
