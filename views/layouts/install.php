<?php

use comyii\user\Module;
use comyii\user\widgets\Logo;
use kartik\helpers\Html;
use kartik\alert\AlertBlock;
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
    <div class="<?= isset($this->params['install-mode']) ? 'y2u-container' : 'y2u-container-pad' ?>">
        <?= AlertBlock::widget(['delay'=>0]) ?>
        <?php $this->beginBody(); ?>
        <?= $content ?>
        <?php $this->endBody(); ?>
    </div>
</body>
    </html>
<?php $this->endPage(); ?>
