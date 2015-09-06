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
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'password_fail_attempts'], 'integer'],
            [['username', 'email', 'password_hash', 'auth_key', 'activation_key', 'reset_key', 'password_reset_on', 'created_on', 'updated_on', 'last_login_on', 'last_login_ip'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'password_fail_attempts' => $this->password_fail_attempts,
            'password_reset_on' => $this->password_reset_on,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
            'last_login_on' => $this->last_login_on,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'activation_key', $this->activation_key])
            ->andFilterWhere(['like', 'reset_key', $this->reset_key])
            ->andFilterWhere(['like', 'last_login_ip', $this->last_login_ip]);

        return $dataProvider;
    }
}
