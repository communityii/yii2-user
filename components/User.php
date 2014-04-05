<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\components\User;

use Yii;

/**
 * User authentication component
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
    public $loginUrl = ['/user/auth/login'];

    /**
     * Initializes the User component
     */
    public function init()
    {
        if ($this->identityClass == null) {
            $this->identityClass = 'communityii\user\models\User';
        }
        parent::init();
    }
}
