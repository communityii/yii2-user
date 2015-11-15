<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\widgets;

use Yii;
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
     * @var bool whether the nav items labels should be HTML-encoded.
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
        /**
         * @var Module $m
         */
        $m = Yii::$app->getModule('user');
        $settings = $this->user === null ? [] : $m->getEditSettingsAdmin($this->user);
        $this->items[] = [
            'label' => $m->icon('list') . Yii::t('user', 'Users'),
            'url' => [$m->actionSettings[Module::ACTION_ADMIN_INDEX]],
            'active' => ($this->ui === 'list'),
            'linkOptions' => ['title' => Yii::t('user', 'View user listing')]
        ];
        if ($m->checkSettings($settings, 'createUser')) {
            $this->items[] = [
                'label' => $m->icon('plus') . Yii::t('user', 'Create'),
                'url' => [$m->actionSettings[Module::ACTION_ADMIN_CREATE]],
                'active' => ($this->ui === 'create'),
                'linkOptions' => ['title' => Yii::t('user', 'Create new user')]
            ];
        }
        if (empty($settings) || $this->user->isNewRecord) {
            parent::init();
            return;
        }
        if ($m->checkSettings($settings, 'changeUsername') || $m->checkSettings($settings, 'changeEmail')) {
            $this->items[] = [
                'label' => $m->icon('pencil') . Yii::t('user', 'Edit'),
                'url' => [$m->actionSettings[Module::ACTION_ADMIN_UPDATE], 'id' => $this->user->id],
                'active' => ($this->ui === 'edit'),
                'linkOptions' => ['title' => Yii::t('user', 'Edit user profile')]
            ];
        }
        $this->items[] = [
            'label' => $m->icon('wrench') . Yii::t('user', 'Manage'),
            'url' => [$m->actionSettings[Module::ACTION_ADMIN_VIEW], 'id' => $this->user->id],
            'active' => ($this->ui === 'manage'),
            'linkOptions' => ['title' => Yii::t('user', 'Administer user profile')]
        ];
        $this->items[] = [
            'label' => $m->icon('eye-open') . Yii::t('user', 'Profile'),
            'url' => [$m->actionSettings[Module::ACTION_PROFILE_VIEW], 'id' => $this->user->id],
            'linkOptions' => ['title' => Yii::t('user', 'View user profile')]
        ];
        parent::init();
    }
}
