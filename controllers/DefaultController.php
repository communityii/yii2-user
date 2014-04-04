<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace communityii\user\controllers;

use Yii;
use communityii\user\Module;

/**
 * Default controller determining the landing page/action for the module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DefaultController extends BaseController
{
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->forward(Module::ACTION_LOGIN);
        } else {
            return $this->forward(Module::ACTION_PROFILE_VIEW);
        }
    }
}