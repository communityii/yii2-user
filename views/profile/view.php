<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */


use yii\web\View;
use comyii\user\models\User;
use comyii\user\models\UserProfile;
use comyii\user\models\SocialProfile;
use comyii\user\widgets\UserMenu;
use comyii\user\widgets\ProfileView;

/**
 * @var View          $this
 * @var User          $model
 * @var UserProfile   $profile
 * @var SocialProfile $social
 * @var mixed         $settings
 */

$this->title = Yii::t('user', 'Profile') . ' (' . $model->username . ')';
$this->params['breadcrumbs'][] = $model->username;
?>
    <div class="page-header">
        <div class="pull-right"><?= UserMenu::widget(['ui' => 'view', 'user' => $model->id]) ?></div>
        <h1><?= $this->title ?></h1>
    </div>
<?php
echo ProfileView::widget([
    'model' => $model,
    'social' => $social,
    'profile' => $profile,
]);