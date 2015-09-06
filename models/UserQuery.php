<?php

/**
 * @copyright Copyright &copy; communityii, 2014
 * @package yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */

namespace comyii\user\models;

use Yii;

/**
 * This is the query class for the `User` Model
 *
 * @method \comyii\user\models\User|array|null one($db = null)
 * @method \comyii\user\models\User[]|array all($db = null)
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class UserQuery extends \yii\db\ActiveQuery
{
    public function pending()
    {
        $this->andWhere(['status' => User::STATUS_PENDING]);
        return $this;
    }

    public function inactive()
    {
        $this->andWhere(['status' => User::STATUS_INACTIVE]);
        return $this;
    }

    public function superuser()
    {
        $this->andWhere(['status' => User::STATUS_SUPERUSER]);
        return $this;
    }

    public function active()
    {
        $this->andWhere(['in', 'status', [User::STATUS_ACTIVE, User::STATUS_SUPERUSER, User::STATUS_ADMIN]]);
        return $this;
    }
    
    public function admin()
    {
        $this->andWhere(['in', 'status', [User::STATUS_ADMIN, User::STATUS_SUPERUSER]]);
        return $this;
    }
}
