<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\events\admin;

use comyii\user\events\ViewEvent;

/**
 * Class IndexEvent is used for triggering events in user admin index action.
 *
 * @package comyii\user\events\admin
 */
class IndexEvent extends ViewEvent
{
    /**
     * @var \yii\db\ActiveQuery the search model
     */
    public $searchModel;

    /**
     *
     * @var \yii\data\BaseDataProvider the data provider
     */
    public $dataProvider;
}
