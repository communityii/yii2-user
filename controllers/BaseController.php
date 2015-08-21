<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use comyii\user\Module;
use comyii\user\models\User;

/**
 * Base controller for all controllers in the user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BaseController extends \yii\web\Controller
{

    /**
     * This method is invoked right before an action within this module is executed.
     *
     * @param Action $action the action to be executed.
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->validateInstallation();
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Validates the module installation
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    protected function validateInstallation() {
        if (isset($this->module->installAccessCode)) {
            if (Yii::$app->db->getTableSchema('{{%user}}') == null) {
                throw new InvalidConfigException('User table schema not found. Ensure the database migration has been run successfully for this module.');
            }
            if (!$this->module->hasSuperUser() && strpos(Yii::$app->request->getPathInfo(), 'user/install') === false) {
                return $this->redirect(['install/index']);
            }
        }
        elseif (!$this->module->hasSuperUser()) {
            throw new InvalidConfigException('You must setup the installAccessCode in the configuration file in order to install the yii2-user module.');
        }
    }

    /**
     * Forwards to a specified action based on the route set in module action settings.
     * If the route is not found in the module config, will use the passed route string.
     *
     * @param string|array $route the action route
     * @param array $params the action parameters
     */
    protected function forward($route, $params = [])
    {
        $route = ArrayHelper::getValue($this->module->actionSettings, $route, $route);
        if (!is_array($route)) {
            $route = [$route];
        }
        return $this->redirect(array_merge($route, $params));
    }

    /**
     * Redirect users based on their authentication status
     *
     * @return \yii\web\Response
     */
    protected function safeRedirect()
    {
        if (Yii::$app->user->isGuest) {
            $this->validateInstallation();
            return $this->forward(Module::ACTION_LOGIN);
        } else {
            return $this->forward(Module::ACTION_PROFILE_VIEW);
        }
    }

    /**
     * Gets the configuration for a module array param
     *
     * @param string $setting the name of the specific setting in module config
     * @param string $param the name of the parameter
     * @param string $default the default value
     * @return mixed the configuration value
     */
    protected function getConfig($setting, $param, $default = null)
    {
        if (empty($this->module->$setting)) {
            return $default;
        }
        return ArrayHelper::getValue($this->module->$setting, $param, $default);
    }
}