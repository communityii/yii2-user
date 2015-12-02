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

use comyii\user\components\Actions;
use comyii\user\components\ArrayComponent;
use comyii\user\Module;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class Buttons the button settings for the module.
 * 
 * @package comyii\user\components
 */
class Buttons extends ArrayComponent
{
    const BTN_HOME = 400;               // back to home page
    const BTN_BACK = 401;               // back to previous page
    const BTN_RESET_FORM = 402;         // reset form button
    const BTN_SUBMIT_FORM = 403;        // submit button
    const BTN_FORGOT_PASSWORD = 404;    // forgot password link
    const BTN_ALREADY_REGISTERED = 405; // already registered link
    const BTN_LOGIN = 406;              // login submit button
    const BTN_LOGOUT = 407;             // logout link
    const BTN_NEW_USER = 408;           // new user registration link
    const BTN_REGISTER = 409;           // registration submit button

    /**
     * @var string the name of the property to store the array items 
     */
    protected $_containerName = 'buttons';

    /**
     * @var string the current item property 
     */
    protected $_currentName = 'button';

    /**
     * @var array the button configurations
     */
    public $buttons;
    
    /**
     * @var array the current button
     */
    public $button;

    /**
     * Construct the buttons component
     * 
     * @param array|null $config
     */
    public function __construct($config = array())
    {
        $this->buttons = $this->getDefaultButtonConfig();
        if (isset($config['buttons'])) {
            $this->buttons = array_replace_recursive($this->buttons, $config['buttons']);
        }
    }

    /**
     * Gets the default button configuration
     * 
     * @return array the default button configuration
     */
    protected static function getDefaultButtonConfig()
    {
        return [
            self::BTN_HOME => [
                'label' => Yii::t('user', 'Home'),
                'icon' => 'home',
                'action' => '/',
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Back to home'),
                ],
            ],
            self::BTN_BACK => [
                'label' => Yii::t('user', 'Return'),
                'icon' => 'arrow-left',
                'action' => Yii::$app->user->returnUrl,
                'options' => ['class' => 'btn btn-link y2u-link'],
            ],
            self::BTN_RESET_FORM => [
                'type' => 'reset',
                'label' => Yii::t('user', 'Reset Form'),
                'icon' => 'repeat',
                'options' => ['class' => 'btn btn-default'],
            ],
            self::BTN_SUBMIT_FORM => [
                'type' => 'submit',
                'label' => Yii::t('user', 'Submit'),
                'icon' => 'save',
                'options' => ['class' => 'btn btn-primary'],
            ],
            self::BTN_FORGOT_PASSWORD => [
                'label' => Yii::t('user', 'Forgot Password?'),
                'icon' => 'info-sign',
                'action' => Actions::ACTION_RECOVERY,
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Recover your lost password')
                ],
            ],
            self::BTN_ALREADY_REGISTERED => [
                'label' => Yii::t('user', 'Already registered?'),
                'icon' => 'hand-up',
                'action' => Actions::ACTION_LOGIN,
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Click here to login')
                ],
            ],
            self::BTN_LOGIN => [
                'type' => 'submit',
                'label' => Yii::t('user', 'Login'),
                'icon' => 'log-in',
                'options' => ['class' => 'btn btn-primary'],
            ],
            self::BTN_LOGOUT => [
                'label' => Yii::t('user', 'Logout'),
                'action' => Actions::ACTION_LOGOUT,
                'icon' => 'log-out',
                'options' => ['class' => 'btn btn-link y2u-link'],
            ],
            self::BTN_NEW_USER => [
                'label' => Yii::t('user', 'New user?'),
                'icon' => 'edit',
                'action' => Actions::ACTION_REGISTER,
                'options' => [
                    'class' => 'btn btn-link y2u-link',
                    'title' => Yii::t('user', 'Register for a new user account')
                ],
            ],
            self::BTN_REGISTER => [
                'type' => 'submit',
                'label' => Yii::t('user', 'Register'),
                'icon' => 'edit',
                'options' => ['class' => 'btn btn-primary'],
            ],
        ];
    }

    /**
     * Generates an action button
     *
     * @param string $key the button identification key
     * @param array  $params the parameters to pass to the button action.
     * @param array  $config the button configuration options to override. You can additionally set the `label` or
     * `icon` here.
     *
     * @return string
     */
    public function button($key, $params = [], $config = [])
    {
        $m = Module::getInstance();
        $btn = ArrayHelper::getValue($this->buttons, $key, []);
        if (empty($btn)) {
            return '';
        }
        $labelNew = ArrayHelper::remove($config, 'label', '');
        $iconNew = ArrayHelper::remove($config, 'icon', '');
        $label = $icon = $action = $type = '';
        $options = [];
        $iconOptions = ['style' => 'margin-right:5px'];
        extract($btn);
        if (!empty($iconNew)) {
            $icon = $iconNew;
        }
        if (!empty($icon)) {
            Html::addCssClass($iconOptions, explode(' ', $m->icons->prefix . $icon));
            $icon = Html::tag('i', '', $iconOptions);
        }
        if (!empty($labelNew)) {
            $label = $labelNew;
        }
        $label = $icon . $label;
        $options = array_replace_recursive($options, $config);
        if (!empty($options['disabled'])) {
            $action = null;
        }
        if (!empty($action)) {
            $action = ArrayHelper::getValue($m->actions, $action, $action);
            $action = Url::to([$action] + $params);
            return Html::a($label, $action, $options);
        }
        if (!empty($type) && $type === 'submit' || $type === 'reset') {
            $type .= 'Button';
        } else {
            $type = 'button';
        }
        return Html::$type($label, $options);
    }
}