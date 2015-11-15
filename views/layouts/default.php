<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

use kartik\helpers\Html;
use kartik\alert\AlertBlock;

/**
 * @var string $content
 */
$this->beginPage();
?>
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
<?php
$this->endPage();
