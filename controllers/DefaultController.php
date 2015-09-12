<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\controllers;

/**
 * Default controller determining the landing page/action for the module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DefaultController extends BaseController
{
    /**
     * By default redirect safely to specific views based on access rules (see `safeRedirect` method).
     *
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        return $this->safeRedirect();
    }
}