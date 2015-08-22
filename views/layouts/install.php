<?php

use comyii\user\Module;
use comyii\user\assets\UserAsset;
use kartik\helpers\Html;
use kartik\alert\AlertBlock;

UserAsset::register($this);
$asset = $this->assetBundles['comyii\user\assets\UserAsset'];
$hasModuleLogo = (isset($this->params['showModuleLogo']) && $this->params['showModuleLogo']);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); ?>
</head>
<body class="y2u-body">
    <div class="<?= $hasModuleLogo ? 'y2u-container' : 'y2u-container-pad' ?>">
        <?php if ($hasModuleLogo): ?>
        <div class="text-center">
            <a href="http://github.com/communityii/yii2-user" class="y2u-title text-warning" target="_blank">
                <?= Html::img($asset->baseUrl . '/img/communityii.png', ['class'=>'y2u-logo']) ?>communityii/yii2-user
            </a>
        </div>
        <?php endif; ?>
        <?= AlertBlock::widget(['delay'=>0]) ?>
        <?php $this->beginBody(); ?>
        <?= $content ?>
        <?php $this->endBody(); ?>
    </div>
</body>
    </html>
<?php $this->endPage(); ?>
