<?php

namespace comyii\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use comyii\user\models\User;

/**
 * UserSearch represents the model behind the search form about `comyii\user\models\User`.
 */
class UserSearch extends User
{
    public function rules()
    {
        return [
            [['id', 'status', 'password_fail_attempts'], 'integer'],
            [['username', 'email', 'password', 'auth_key', 'activation_key', 'reset_key', 'last_login_ip', 'last_login_on', 'password_reset_on', 'created_on', 'updated_on'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'password_fail_attempts' => $this->password_hash_fail_attempts,
            'last_login_on' => $this->last_login_on,
            'password_reset_on' => $this->password_hash_reset_on,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password_hash])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'activation_key', $this->activation_key])
            ->andFilterWhere(['like', 'reset_key', $this->reset_key])
            ->andFilterWhere(['like', 'last_login_ip', $this->last_login_ip]);

        return $dataProvider;
    }
}
