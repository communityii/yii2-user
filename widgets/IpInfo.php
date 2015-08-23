<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use kartik\helpers\Html;
use kartik\base\Widget;

/**
 * IP Info widget for the yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class IpInfo extends Widget
{
    const IP_INFO = 'http://api.hostip.info/get_json.php';
    const IP_FLAG = 'http://api.hostip.info/flag.php';
    
    /**
     * @var string the ip address
     */
    public $ip;
    
    /**
     * @var bool whether to show flag
     */
    public $showFlag = true;
    
    /**
     * @var bool whether to display position coordinates
     */
    public $showPosition = true;
    
    /**
     * @var bool whether to show details on hover of flag
     */
    public $showHoverDetails = true;
    
    /**
     * @var bool whether to return raw json data
     */
    public $getRawJson = false;
    
    public $labels = [];

    public $contentOptions = ['class'=>'table'];
    
    public $flagOptions = ['style' => 'height:18px'];
    
    public $noData;
    
    public $noDataOptions = ['class' => 'text-danger'];
    
    public $options = [];
    
    protected $_defaultLabels = [];
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!isset($this->options['title'])) {
            $this->options['title'] = '<i class="glyphicon glyphicon-info-sign text-info"></i> ' . Yii::t('user', 'IP Information');
        }
        $this->_defaultLabels = [
            'country_code' => Yii::t('user', 'Country Code'),
            'country_name' => Yii::t('user', 'Country Name'),
            'city' => Yii::t('user', 'City'),
            'ip' => Yii::t('user', 'IP Address'),
            'lat' => Yii::t('user', 'Latitude'),
            'lng' => Yii::t('user', 'Longitude')
        ];
        if (empty($this->labels)) {
            $this->labels = $this->_defaultLabels;
        } else {
            foreach ($this->labels as $key => $val) {
                if (empty($val) && isset($this->_defaultLabels[$key])) {
                    $this->labels[$key] = $this->_defaultLabels[$key];
                }
            }
        }
        echo $this->renderWidget();
    }
    
    public function renderWidget()
    {
        $ip = Html::encode($this->ip);
        $params = '?ip=' . $ip;
        if ($this->showPosition) {
            $params .= '&position=true';
        }
        $json = file_get_contents(self::IP_INFO . $params);
        if ($this->getRawJson) {
            return $json;
        }
        $out = Json::decode($json);
        $content = '';
        if (is_array($out)) {
            if (ArrayHelper::getValue($out, 'country_code') == 'XX') {
                $noData = empty($this->noData) ? Yii::t('user', 'No data found') : $this->noData;
                $content = Html::tag('h5', $noData, $this->noDataOptions);
            } else {
                $content = Html::beginTag('table', $this->contentOptions) . "\n";
                foreach ($out as $key => $value) {
                    if (isset($this->labels[$key])) {
                        $key = $this->labels[$key];
                        $content .= "<tr><th>{$key}</th><td>{$value}</td>\n";
                    }
                }
                $content .= "</table>\n";
            }
        }
        if ($this->showFlag) {
            if ($this->showHoverDetails) {
                $view = $this->getView();
                \yii\bootstrap\BootstrapPluginAsset::register($view);
                $this->registerPlugin('popover');
                $this->options['data-toggle'] = 'popover';
                $this->options['data-container'] = 'body';
                $this->options['data-content'] = $content;
                $this->options['tabindex'] = -1;
                $this->options['data-trigger'] = 'focus';
                $this->options['data-html'] = 'true';
            }
            if (!isset($this->options['alt'])) {
                $this->options['alt'] = $ip;
            }
            
            return Html::a(Html::img(self::IP_FLAG . '?ip=' . $ip, $this->flagOptions), '#', $this->options);
        }
        return $content;
    }
}