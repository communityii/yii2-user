<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\social;

use Yii;
use comyii\user\Module;
use comyii\user\models\User;

/**
 * Base trait for authorization clients used in `communityii/yii2-user`
 *
 * @package comyii\user\social
 */
trait ClientTrait
{
    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->getDefaultEmail();
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->getDefaultUsername();
    }

    /**
     * Generates a default email
     *
     * @return string
     */
    protected function getDefaultEmail()
    {
        /**
         * @var \yii\authclient\BaseClient $this
         */
        $attributes = $this->getUserAttributes();
        return $attributes && !empty($attributes['email']) ? $attributes['email'] : null;
    }

    /**
     * Generates a default username
     *
     * @return string
     */
    protected function getDefaultUsername()
    {
        /**
         * @var \yii\authclient\BaseClient $this
         */
        $attributes = $this->getUserAttributes();
        if ($attributes && !empty($attributes['name'])) {
            $username = static::parseUsername($attributes['name']);
            if ($username) {
                return $username;
            } else {
                /** @noinspection PhpUndefinedMethodInspection */
                $email = $this->getEmail();
                $username = substr($email, 0, strrpos($email, '@'));
                return static::parseUsername($username);
            }
        }
        return null;
    }

    /**
     * Generates username and checks if it exists and returns a parsed username
     *
     * @param string $username
     *
     * @return string
     */
    protected static function parseUsername($username)
    {
        /**
         * @var Module $m
         * @var User   $userClass
         */
        $m = Yii::$app->getModule('user');

        $username = str_replace(' ', '_', strtolower($username));
        $userClass = $m->getModelSetting(Module::MODEL_USER);
        if (!$userClass::find()->where(['username' => $username])->exists()) {
            return $username;
        }
        return null;
    }
}
