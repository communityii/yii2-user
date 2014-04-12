<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\widgets;

use Yii;
use yii\base\InvalidConfigException;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\ArrayHelper;

/**
 * Base form widget for the yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BaseForm extends \yii\base\Widget
{
    /**
     * @var yii\base\Model|yii\db\ActiveRecord the model instance
     */
    public $model;

    /**
     * @var array the options and HTML attributes for the ActiveForm
     */
    public $formOptions = ['type' => ActiveForm::TYPE_VERTICAL];

    /**
     * @var array the list of attributes for the form
     * @see \kartik\builder\Form
     */
    public $attributes = [];

    /**
     * @var string the template for rendering the action buttons for the form. The following special
     * variables will be replaced:
     * - '{reset}': will be replaced with the reset button
     * - '{submit}': will be replaced with the submit button
     */
    public $buttons = "{reset} {submit}";

    /**
     * @var array HTML attributes for the form submit button. The following additional options are recognized:
     * - label: string, the label for submit button. This is not HTML encoded.
     * - icon: string, the glyphicon name suffix.
     */
    public $submitButtonOptions = [];

    /**
     * @var array HTML attributes for the reset button. The following additional options are recognized:
     * - label: string, the label for submit button. This is not HTML encoded.
     * - icon: string, the glyphicon name suffix.
     */
    public $resetButtonOptions = [];

    /**
     * @var array|boolean the HTML attributes for the container enclosing the form action buttons.
     * If set to false, no container will used to enclose the buttons. The following additional properties
     * will be recognized:
     * - tag: string, the HTML tag for rendering the container. Defaults to 'span'.
     */
    public $buttonsContainer = [];

    /**
     * @var array the options and HTML attributes for the kartik\builder\Form
     */
    public $options = [];

    /**
     * @var string the parsed HTML markup for the buttons
     */
    protected $_buttons;

    /**
     * @var \kartik\widgets\ActiveForm instance
     */
    protected $_form;

    public function init()
    {
        parent::init();
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        $this->initButtonOptions();
        $this->_form = ActiveForm::begin($this->formOptions);
    }

    public function run()
    {
        $options = ['model' => $model, 'form' => $this->_form, 'attributes' => $this->attributes] + $this->options;x
        echo Form::widget($options);
        if ($buttons != null) {
            $tag = ArrayHelper::remove($this->buttonsContainer, 'tag', 'span');
            echo Html::tag($tag, $this->_buttons, $this->buttonsContainer);
        }
        ActiveForm::end();
    }

    /**
     * Initializes the button options and sets the _buttons
     * property based on `buttons` template setting.
     */
    public function initButtonOptions()
    {
        $this->submitButtonOptions += [
            'label' => Yii::t('user', 'Submit'),
            'icon' => 'ok',
            'class' => 'btn btn-primary',
        ];
        $this->resetButtonOptions += [
            'label' => Yii::t('user', 'Reset'),
            'icon' => 'remove',
            'class' => 'btn btn-reset',
        ];
        $this->_buttons = strtr($this->buttons, [
            '{submit}' => static::getButton('submit', $this->submitButtonOptions),
            '{reset}' => static::getButton('reset', $this->resetButtonOptions)
        ]);
    }

    /**
     * Gets the HTML markup for the action button
     *
     * @param $type string, button type, `reset` or `submit`
     * @param $options the HTML attributes for the button
     * @return string
     */
    protected static function getButton($type, &$options)
    {
        $icon = ArrayHelper::remove($options, 'icon', '');
        if ($icon != '') {
            $icon = '<span class="glyphicon glyphicon-' . $icon . '"></span> ';
        }
        $label = $icon . ArrayHelper::remove($options, 'label', '');
        return ($type == 'reset') ? Html::resetButton($label, $options) : Html::submitButton($label, $options);
    }
}