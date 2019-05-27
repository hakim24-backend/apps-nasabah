<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pencicilan;

/**
 * PencicilanSearch represents the model behind the search form of `app\models\Pencicilan`.
 */
class PencicilanSearch extends Pencicilan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_peminjaman', 'id_pengguna', 'id_jenis_pencicilan'], 'integer'],
            [['tanggal_waktu_cicilan'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Pencicilan::find();

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
            'id_peminjaman' => $this->id_peminjaman,
            'id_pengguna' => $this->id_pengguna,
            'id_jenis_pencicilan' => $this->id_jenis_pencicilan,
            'tanggal_waktu_cicilan' => $this->tanggal_waktu_cicilan,
        ]);

        return $dataProvider;
    }
}
