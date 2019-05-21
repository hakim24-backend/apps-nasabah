<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Peminjaman;

/**
 * PeminjamanSearch represents the model behind the search form of `app\models\Peminjaman`.
 */
class PeminjamanSearch extends Peminjaman
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_nasabah', 'id_jenis_durasi', 'durasi', 'id_pengguna'], 'integer'],
            [['nomor_kontrak', 'nama', 'alamat', 'nik_ktp', 'jaminan','tanggal_waktu_pembuatan'], 'safe'],
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
        $query = Peminjaman::find();

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
            'id_nasabah' => $this->id_nasabah,
            'nominal_peminjaman' => $this->nominal_peminjaman,
            'id_jenis_durasi' => $this->id_jenis_durasi,
            'durasi' => $this->durasi,
            'tanggal_waktu_pembuatan' => $this->tanggal_waktu_pembuatan,
            'id_pengguna' => $this->id_pengguna,
        ]);

        $query->andFilterWhere(['like', 'nomor_kontrak', $this->nomor_kontrak])
            ->andFilterWhere(['like', 'nama', $this->nama])
            ->andFilterWhere(['like', 'alamat', $this->alamat])
            ->andFilterWhere(['like', 'nik_ktp', $this->nik_ktp])
            ->andFilterWhere(['like', 'jaminan', $this->jaminan]);

        return $dataProvider;
    }
}
