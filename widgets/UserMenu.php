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

/**
 * User profile actions menu
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class UserMenu extends Nav
{
    /**
     * @var integer the user id
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
        $currUser = Yii::$app->user;
        $isAdmin = $currUser->isAdmin || $currUser->isSuperuser;
        if ($currUser->id == $this->user) {
            $this->items[] = [
                'label' => $m->icon('eye-open') . Yii::t('user', 'View'),
                'url' => [$m->actionSettings[Module::ACTION_PROFILE_INDEX]],
                'active' => ($this->ui === 'view'),
                'linkOptions' => ['title' => Yii::t('user', 'View user profile')]
            ];
            $this->items[] = [
                'label' => $m->icon('pencil') . Yii::t('user', 'Edit'),
                'url' => [$m->actionSettings[Module::ACTION_PROFILE_UPDATE]],
                'active' => ($this->ui === 'edit'),
                'linkOptions' => ['title' => Yii::t('user', 'Edit user profile')]
            ];
            $this->items[] = [
                'label' => $m->icon('lock') . Yii::t('user', 'Password'),
                'url' => [$m->actionSettings[Module::ACTION_ACCOUNT_PASSWORD]],
                'active' => ($this->ui === 'password'),
                'linkOptions' => ['title' => Yii::t('user', 'Change user password')]
            ];
        } elseif ($isAdmin) {
            $this->items[] = [
                'label' => $m->icon('eye-open') . Yii::t('user', 'View'),
                'url' => [$m->actionSettings[Module::ACTION_PROFILE_INDEX]],
                'active' => ($this->ui === 'view'),
                'linkOptions' => ['title' => Yii::t('user', 'View user profile')]
            ];
        }
        if ($isAdmin) {
            $this->items[] = [
                'label' => $m->icon('wrench') . Yii::t('user', 'Manage'),
                'url' => [$m->actionSettings[Module::ACTION_ADMIN_VIEW], 'id' => $this->user],
                'linkOptions' => ['title' => Yii::t('user', 'Administer user profile')]
            ];
        }
        parent::init();
    }
}
