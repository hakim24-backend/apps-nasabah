<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nasabah_riwayat_nomor_telepon".
 *
 * @property int $id
 * @property int $id_nasabah
 * @property string $nomor_telepon
 * @property string $tanggal_waktu_pembuatan
 *
 * @property Nasabah $nasabah
 */
class NasabahRiwayatNomorTelepon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nasabah_riwayat_nomor_telepon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_nasabah'], 'integer'],
            [['tanggal_waktu_pembuatan'], 'safe'],
            [['nomor_telepon'], 'string', 'max' => 20],
            [['id_nasabah'], 'exist', 'skipOnError' => true, 'targetClass' => Nasabah::className(), 'targetAttribute' => ['id_nasabah' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_nasabah' => 'Id Nasabah',
            'nomor_telepon' => 'Nomor Telepon',
            'tanggal_waktu_pembuatan' => 'Tanggal Waktu Pembuatan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNasabah()
    {
        return $this->hasOne(Nasabah::className(), ['id' => 'id_nasabah']);
    }
}
