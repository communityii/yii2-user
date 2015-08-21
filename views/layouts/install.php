<?php

use comyii\user\Module;
use comyii\user\assets\UserAsset;
use kartik\helpers\Html;
use kartik\widgets\AlertBlock;

UserAsset::register($this);
$asset = $this->assetBundles['comyii\user\assets\UserAsset'];
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); ?>
</head>
<body class="y2u-padding">
    <div class="y2u-container">
        <div class="text-center">
            <?= Html::img($asset->baseUrl . '/img/communityii.png', ['class'=>'y2u-logo']) . Module::PROJECT_PAGE ?>
        </div>
        <?= AlertBlock::widget(['delay'=>0]) ?>
        <?php $this->beginBody(); ?>
        <?= $content ?>
        <?php $this->endBody(); ?>
    </div>
</body>
    </html>
<?php $this->endPage(); ?>
