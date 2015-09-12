<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\assets;

/**
 * Asset bundle for user module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class AdminAsset extends \kartik\base\AssetBundle
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__);
        $this->setupAssets('js', ['js/admin']);
        parent::init();
    }

}