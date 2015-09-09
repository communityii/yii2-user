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
use yii\bootstrap\Nav;
use comyii\user\Module;
use comyii\user\models\User;

/**
 * User profile actions menu
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class AdminMenu extends Nav
{
    /**
     * @var User the user model
     */
    public $user;
    
    /**
     * @var boolean whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = false;
    
    /**
     * @var string the user interface currently being rendered
     */
    public $ui;

    /**
     * @inheritdoc
     */
    public $options = ['class' => 'nav-pills'];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $m = Yii::$app->getModule('user');
        $currUser = Yii::$app->user;
        $settings = $this->user === null ? [] : $m->getEditSettingsAdmin($this->user);
        $this->items = [
            [
               'label' => $m->icon('list') . Yii::t('user', 'User List'),
               'url' => [$m->actionSettings[Module::ACTION_ADMIN_LIST]],
               'active' => ($this->ui === 'list'),
               'linkOptions' => ['title' => Yii::t('user', 'View user listing')]
            ],
        ];
        if (!empty($settings)) {
            $this->items[] = [
               'label' => $m->icon('wrench') . Yii::t('user', 'Manage'),
               'url' => [$m->actionSettings[Module::ACTION_ADMIN_MANAGE], 'id' => $this->user->id],
               'active' => ($this->ui === 'manage'),
               'linkOptions' => ['title' => Yii::t('user', 'Administer user profile')]
            ];
        }
        if ($m->checkSettings($settings, 'createUser')) {
            $this->items[] = [
               'label' => $m->icon('plus') . Yii::t('user', 'Create'),
               'url' => [$m->actionSettings[Module::ACTION_ADMIN_CREATE]],
               'active' => ($this->ui === 'create'),
               'linkOptions' => ['title' => Yii::t('user', 'Create new user')]
            ];
        }
        if (empty($settings)) {
            parent::init();
            return;
        }
        if ($m->checkSettings($settings, 'changeUsername') || $m->checkSettings($settings, 'changeEmail')) {
            $this->items[] = [
               'label' => $m->icon('pencil') . Yii::t('user', 'Edit'),
               'url' => [$m->actionSettings[Module::ACTION_ADMIN_EDIT]],
               'active' => ($this->ui === 'edit'),
               'linkOptions' => ['title' => Yii::t('user', 'Edit user profile')]
            ];
        }
        
        $this->items[] = [
           'label' => $m->icon('eye-open') . Yii::t('user', 'Profile'),
           'url' => [$m->actionSettings[Module::ACTION_PROFILE_INDEX]],
           'linkOptions' => ['title' => Yii::t('user', 'View user profile')]
        ];
        parent::init();
    }
}