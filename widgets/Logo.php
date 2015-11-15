<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use comyii\user\assets\UserAsset;

/**
 * Logo for yii2-user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Logo extends Widget
{
    const LOGO_IMAGE = '/img/communityii.png';

    /**
     * @var string the url to launch. If not set defaults to home url.
     */
    public $url;

    /**
     * @var string the title to display beside the logo. If not set
     * defaults to app name.
     */
    public $title;

    /**
     * @var array the HTML attributes for the logo
     */
    public $imageOptions = [
        'alt' => 'logo',
        'class' => 'y2u-logo'
    ];

    /**
     * @var array the HTML attributes for the logo link
     */
    public $options = [
        'class' => 'y2u-title text-muted',
        'target' => '_blank'
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        if (!isset($this->url)) {
            $this->url = Url::home();
        }
        if (!isset($this->title)) {
            $this->title = Yii::$app->name;
        }
        UserAsset::register($view);
        $asset = $view->assetBundles[UserAsset::classname()];
        $image = Html::img($asset->baseUrl . self::LOGO_IMAGE, $this->imageOptions);
        echo Html::a($image . $this->title, $this->url, $this->options);
    }
}
