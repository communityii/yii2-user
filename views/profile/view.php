<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */


use comyii\user\widgets\UserMenu;

/**
 * @var yii\web\View $this
 * @var comyii\user\models\User $model
 */

$m = $this->context->module;
$this->title =  Yii::t('user', 'Profile') . ' (' . $model->username . ')';
$this->params['breadcrumbs'][] = $model->username;
?>
<div class="page-header">
    <div class="pull-right"><?= UserMenu::widget(['ui' => 'view', 'user' => $model->id]) ?></div>
    <h1><?= $this->title ?></h1>
</div>
<?php
echo comyii\user\widgets\ProfileView::widget([
    'model'=>$model,
    'social'=>$social,
    'profile'=>$profile,
]);