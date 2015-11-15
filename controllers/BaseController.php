<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\controllers;

use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use comyii\user\events\Event;
use comyii\user\events\AccountEvent;
use comyii\user\events\ExceptionEvent;
use comyii\user\components\EmailException;
use comyii\user\Module;

/**
 * Base controller for all controllers in the user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BaseController extends Controller
{
    /**
     * @var Module the user module
     */
    protected $_module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_module = Yii::$app->getModule('user');
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->validateInstallation();
            $route = $this->id . '/' . $action->id;
            $settings = array_flip($this->_module->getDefaultActionSettings());
            if (isset($settings[$route])) {
                $actionId = $settings[$route];
                $configRoute = $this->fetchAction($actionId);
                $url1 = Url::to([$route], true);
                $url2 = Url::to([$configRoute], true);
                if ($url1 !== $url2) {
                    throw new ForbiddenHttpException('The requested url cannot be accessed.');
                }
            }
            $this->_module->trigger(Module::EVENT_BEFORE_ACTION);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Modified render method to display the view. This takes in the view id as a parameter instead of
     * view name.
     *
     * @param int   $view the view identifier as set in one of Module::VIEW constants
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * These parameters will not be available in the layout.
     *
     * @return string the rendering result
     */
    public function display($view, $params = [])
    {
        if (!empty($this->_module->getLayout($view))) {
            $this->layout = $this->_module->getLayout($view);

        }
        $view = $this->fetchView($view);
        return parent::render($view, $params);
    }

    /**
     * Validates the module installation
     *
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    protected function validateInstallation()
    {
        if (isset($this->_module->installAccessCode)) {
            $app = Yii::$app;
            if ($app->db->getTableSchema('{{%user}}') == null) {
                throw new InvalidConfigException('User table schema not found. Ensure the database migration has been run successfully for this module.');
            }
            if (!$this->_module->hasSuperUser() && strpos($app->request->getPathInfo(), 'user/install') === false) {
                return $this->redirect(['/user/install/index']);
            }
        } elseif (!$this->_module->hasSuperUser()) {
            throw new InvalidConfigException('Module installation for "communityii\yii2-user" cannot proceed. You must set a valid "installAccessCode" for the "user" module in your application configuration file.');
        }
        return null;
    }

    /**
     * Forwards to a specified action based on the route set in module action settings. If the route is not found in
     * the module config, will use the passed route string.
     *
     * @param string|array $route the action route
     * @param array        $params the action parameters
     *
     * @return mixed
     */
    protected function forward($route, $params = [])
    {
        $route = ArrayHelper::getValue($this->_module->actionSettings, $route, $route);
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
            $out = $this->validateInstallation();
            return ($out === null) ? $this->forward(Module::ACTION_LOGIN) : $out;
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
     *
     * @return mixed the configuration value
     */
    protected function getConfig($setting, $param, $default = null)
    {
        if (empty($this->_module->$setting)) {
            return $default;
        }
        return ArrayHelper::getValue($this->_module->$setting, $param, $default);
    }

    /**
     * Parse url from module config
     *
     * @param mixed  $value the name of the specific setting in module config or the URL
     * @param string $param the name of the parameter - if this is not passed, the `value` param will be parsed
     * as a fetch action from Module config
     *
     * @return array the configuration value
     */
    protected function fetchUrl($value, $param = null)
    {
        $url = $param === null ? $this->fetchAction($value) : $this->getConfig($value, $param);
        return is_array($url) ? $url : [$url];
    }


    /**
     * Gets the view from the module view settings
     *
     * @param string $view the identifier of the view
     *
     * @return string the view name
     */
    protected function fetchView($view)
    {
        if (is_string($view)) {
            return $view;
        }
        if (isset(Yii::$app->user->type) && isset($this->_module->viewSettings[Yii::$app->user->type][$view])) {
            return $this->_module->viewSettings[Yii::$app->user->type][$view];
        }
        return $this->getConfig('viewSettings', $view);
    }

    /**
     * Gets the view from the module action settings
     *
     * @param string $action the identifier of the action
     *
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
     *
     * @return string the model class
     */
    protected function fetchModel($model)
    {
        return $this->getConfig('modelSettings', $model);
    }

    /**
     * Redirects based on availability of event redirectUrl.
     *
     * @param AccountEvent $event
     * @param mixed        $response
     * @param boolean      $flag whether to directly return the response or treat it as a URL and redirect. Defaults to
     * `true` whereby directly the response is returned.
     *
     * @return mixed
     */
    protected function eventRedirect($event, $response, $flag = true)
    {
        if (!empty($event->redirectUrl)) {
            return $this->redirect($event->redirectUrl);
        }
        return $flag ? $response : $this->redirect($response);
    }

    /**
     * Raise an exception event trigger
     *
     * @param Exception $exception
     * @param Event     $event
     *
     * @return null|Transaction
     */
    protected function raise($exception, $event)
    {
        $ev = new ExceptionEvent();
        $ev->event = $event;
        $ev->exception = $exception;
        $ev->controller = $this;
        $this->_module->trigger(Module::EVENT_EXCEPTION, $event);
    }

    /**
     * @param array $behaviors
     *
     * @return array
     * @throws InvalidConfigException
     */
    protected function mergeBehaviors($behaviors = [])
    {
        return Module::mergeDefault($this->_module->getControllerBehaviors($this->id), $behaviors);
    }

    /**
     * Handle exception
     *
     * @param Exception $e
     */
    public function handleException($e)
    {
        if ($e instanceof EmailException) {
            $this->_module->trigger(Module::EVENT_EMAIL_FAILED, $e->event);
        }
        if (property_exists($e, 'event') && property_exists($e->event, 'message')) {
            static::setFlash($e->event);
        }
    }

    /**
     * Initiates a DB transaction based on event's `useTransaction` property
     *
     * @param Event $event
     *
     * @return null|Transaction
     */
    protected static function tranInit($event)
    {
        return $event->useTransaction ? Yii::$app->db->beginTransaction() : null;
    }

    /**
     * Commits a DB transaction if valid
     *
     * @param null|Transaction $transaction
     */
    protected static function tranCommit($transaction)
    {
        if ($transaction && $transaction instanceof Transaction) {
            $transaction->commit();
        }
    }

    /**
     * Rollsback a DB transaction if valid
     *
     * @param null|Transaction $transaction
     */
    protected static function tranRollback($transaction)
    {
        if ($transaction && $transaction instanceof Transaction) {
            $transaction->rollback();
        }
    }

    /**
     * Sets a flash message based on event `flashType` and `message` properties.
     *
     * @param Event $event
     *
     * @return null|Transaction
     */
    protected static function setFlash($event)
    {
        if ($event->message) {
            Yii::$app->session->setFlash($event->flashType, $event->message);
        }
    }
}
