<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\tests\codeception\_app\models;

/**
 * TestUser model
 */
class TestUser extends \comyii\user\models\User
{

    const TYPE_VENDOR = 1;
    const TYPE_CUSTOMER = 2;

}
