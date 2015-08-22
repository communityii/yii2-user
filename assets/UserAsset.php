<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
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
class UserAsset extends \kartik\base\AssetBundle
{
   
    public function init()
    {
        $this->setSourcePath(__DIR__);
        $this->setupAssets('css', ['css/main']);
        parent::init();
    }

}