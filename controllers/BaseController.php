<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
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
     * @inheritdoc
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
     * Modified render method to display the view. This takes in the view id as a parameter instead of 
     * view name.
     * @param int $view the view identifier as set in one of Module::VIEW constants
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * These parameters will not be available in the layout.
     * @return string the rendering result
     */
    public function display($view, $params = []) {
        if (!empty($this->module->layoutSettings[$view])) {
            $this->layout = $this->module->layoutSettings[$view];
            
        }
        $view = $this->fetchView($view);
        return parent::render($view, $params);
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
            throw new InvalidConfigException('Module installation for "communityii\yii2-user" cannot proceed. You must set a valid "installAccessCode" for the "user" module in your application configuration file.');
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
        $user = Yii::$app->user;
        if ($user->isGuest) {
            $this->validateInstallation();
            return $this->forward(Module::ACTION_LOGIN);
        } else {
            if (!empty($user->returnUrl)) {
                return $this->redirect($user->returnUrl);
            }
            if ($user->isAdmin || $user->isSuperuser) {
                return $this->forward(Module::ACTION_ADMIN_INDEX, ['id' => $user->id]);
            } else {
                return $this->forward(Module::ACTION_PROFILE_INDEX);
            }
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

    /**
     * Gets the view from the module view settings
     *
     * @param string $view the identifier of the view
     * @return string the view name
     */
    protected function fetchView($view)
    {
        return $this->getConfig('viewSettings', $view);
    }

    /**
     * Gets the view from the module action settings
     *
     * @param string $action the identifier of the action
     * @return string the action name
     */
    protected function fetchAction($action)
    {
        return $this->getConfig('actionSettings', $action);
    }

    /**
     * Gets the model from the module model settings
     *
     * @param string $model the identifier of the model
     * @return string the model class
     */
    protected function fetchModel($model)
    {
        return $this->getConfig('modelSettings', $model);
    }
}