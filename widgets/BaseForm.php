<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use comyii\user\Module;
use kartik\helpers\Html;
use kartik\form\ActiveForm;
use kartik\builder\Form;

/**
 * Base form widget for the yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BaseForm extends Widget
{
    /**
     * @var string the form title
     */
    public $title = '';

    /**
     * @var \yii\base\Model|\yii\db\ActiveRecord the model instance
     */
    public $model;

    /**
     * @var array the options and HTML attributes for the ActiveForm
     */
    public $formOptions = [];

    /**
     * @var array the list of attributes for the form
     * @see \kartik\builder\Form
     */
    public $attributes = [];

    /**
     * @var array the options and HTML attributes for the footer. The following
     * special attributes are parsed:
     * `tag`: string, the HTML tag to render the footer. Defaults to `div`.
     */
    public $footerOptions = ['class' => 'y2u-box-footer'];

    /**
     * @var string the content to render for the left footer
     */
    public $leftFooter = '';

    /**
     * @var string the content to render for the right footer
     */
    public $rightFooter = '';

    /**
     * @var array the widget configuration options for `kartik\builder\Form`
     */
    public $options = ['options' => ['class' => 'y2u-padding']];

    /**
     * @var string template to render the widget. The tag `{fields}` will be replaced
     * with form fields, while the tag `{footer}` will be replaced with the output
     * of footer template.
     */
    public $template;

    /**
     * @var string template to render the footer. The tag `{left}` will be replaced
     * with the `leftFooter` and the tag `{right}` will be replaced with the
     * `rightFooter`.
     */
    public $footerTemplate;

    /**
     * @var ActiveForm the form instance
     */
    protected $_form;

    /**
     * @var Module the user module configuration
     */
    protected $_module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_module = Yii::$app->getModule('user');
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (!isset($this->formOptions['type'])) {
            $this->formOptions['type'] = ActiveForm::TYPE_VERTICAL;
        }
        if (!isset($this->formOptions['fieldConfig'])) {
            $this->formOptions['fieldConfig'] = ['autoPlaceholder' => true];
        }
        if (!isset($this->template)) {
            $this->template = "<legend>{$this->title}</legend>\n{fields}\n{footer}";
        }
        if (!isset($this->footerTemplate)) {
            $this->footerTemplate = <<< HTML
<div class="row">
    <div class="col-sm-6 text-left">
        {left}
    </div>
    <div class="col-sm-6 text-right">
        {right}
    </div>
</div>
HTML;
        }
        $this->_form = ActiveForm::begin($this->formOptions);
        $options = [
                'model' => $this->model,
                'form' => $this->_form,
                'attributes' => $this->attributes
            ] + $this->options;
        echo strtr($this->template, [
            '{fields}' => Form::widget($options),
            '{footer}' => $this->renderFooter()
        ]);
        ActiveForm::end();
    }

    /**
     * Renders the footer
     * @return string
     */
    public function renderFooter()
    {
        $footer = strtr($this->footerTemplate, [
            '{left}' => $this->leftFooter,
            '{right}' => $this->rightFooter,
        ]);
        $tag = ArrayHelper::remove($this->footerOptions, 'tag', 'div');
        return Html::tag($tag, $footer, $this->footerOptions);
    }
 
    /**
     * Merges the passed attributes within the attributes configuration
     * @param array the passed attributes
     */
    protected function mergeAttributes($attributes = [])
    {
        if (empty($this->attributes)) {
            $this->attributes = $attributes;
        } else {
            $this->attributes = array_replace_recursive($attributes, $this->attributes);
        }
    }
}