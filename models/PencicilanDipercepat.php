<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pencicilan_dipercepat".
 *
 * @property int $id
 * @property int $id_peminjaman
 * @property string $tanggal_pencicilan
 * @property double $nominal_pencicilan
 * @property int $durasi_terbayar
 * @property int $id_status_bayar
 *
 * @property Peminjaman $peminjaman
 * @property PencicilanStatusBayar $statusBayar
 */
class PencicilanDipercepat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pencicilan_dipercepat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_peminjaman', 'durasi_terbayar', 'id_status_bayar'], 'integer'],
            [['tanggal_pencicilan'], 'safe'],
            [['nominal_pencicilan'], 'number'],
            [['id_peminjaman'], 'exist', 'skipOnError' => true, 'targetClass' => Peminjaman::className(), 'targetAttribute' => ['id_peminjaman' => 'id']],
            [['id_status_bayar'], 'exist', 'skipOnError' => true, 'targetClass' => PencicilanStatusBayar::className(), 'targetAttribute' => ['id_status_bayar' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_peminjaman' => 'Id Peminjaman',
            'tanggal_pencicilan' => 'Tanggal Pencicilan',
            'nominal_pencicilan' => 'Nominal Pencicilan',
            'durasi_terbayar' => 'Durasi Terbayar',
            'id_status_bayar' => 'Id Status Bayar',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjaman()
    {
        return $this->hasOne(Peminjaman::className(), ['id' => 'id_peminjaman']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusBayar()
    {
        return $this->hasOne(PencicilanStatusBayar::className(), ['id' => 'id_status_bayar']);
    }
}
