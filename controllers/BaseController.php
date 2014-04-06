<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use communityii\user\Module;

/**
 * Base controller for all controllers in the user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BaseController extends \yii\web\Controller
{
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
        return $this->redirect($route, $params);
    }

    /**
     * Redirect users based on their authentication status
     *
     * @return \yii\web\Response
     */
    protected function safeRedirect()
    {
        if (Yii::$app->user->isGuest) {
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