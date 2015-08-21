<?php

namespace comyii\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use comyii\user\models\UserBanLog;

/**
 * UserBanLogSearch represents the model behind the search form about `\comyii\user\models\UserBanLog`.
 */
class UserBanLogSearch extends UserBanLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['user_ip', 'ban_reason', 'revoke_reason', 'banned_till', 'created_on', 'updated_on'], 'safe'],
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
        $query = UserBanLog::find();

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
            'user_id' => $this->user_id,
            'banned_till' => $this->banned_till,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
        ]);

        $query->andFilterWhere(['like', 'user_ip', $this->user_ip])
            ->andFilterWhere(['like', 'ban_reason', $this->ban_reason])
            ->andFilterWhere(['like', 'revoke_reason', $this->revoke_reason]);

        return $dataProvider;
    }
}
