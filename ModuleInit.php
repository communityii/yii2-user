<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;
use comyii\user\Module;

/**
 * Module initialization bootstrap class for yii2-user module. This class assigns and enables module specific URL rules.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class ModuleInit implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if (!$app instanceof \yii\web\Application || !$app->hasModule('user')) {
            return;
        }
        /**
         * @var Module $m
         */
        $m = $app->getModule('user');
        if (!$m instanceof Module) {
            return;
        }
        $config = ['prefix' => $m->urlPrefix, 'rules' => $m->urlRules];
        if ($m->urlPrefix != 'user') {
            $config['routePrefix'] = 'user';
        }
        $app->urlManager->addRules([new GroupUrlRule($config)], false);
    }
}