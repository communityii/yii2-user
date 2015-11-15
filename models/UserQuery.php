<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\models;

use Yii;
use comyii\user\Module;
use yii\db\ActiveQuery;

/**
 * This is the query class for the `User` Model
 *
 * @method User|array|null one($db = null)
 * @method User[]|array all($db = null)
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class UserQuery extends ActiveQuery
{
    public function pending()
    {
        $this->andWhere(['status' => Module::STATUS_PENDING]);
        return $this;
    }

    public function inactive()
    {
        $this->andWhere(['status' => Module::STATUS_INACTIVE]);
        return $this;
    }

    public function superuser()
    {
        $this->andWhere(['status' => Module::STATUS_SUPERUSER]);
        return $this;
    }

    public function active()
    {
        $this->andWhere(['in', 'status', [Module::STATUS_ACTIVE, Module::STATUS_SUPERUSER, Module::STATUS_ADMIN]]);
        return $this;
    }
    
    public function admin()
    {
        $this->andWhere(['in', 'status', [Module::STATUS_ADMIN, Module::STATUS_SUPERUSER]]);
        return $this;
    }
}
