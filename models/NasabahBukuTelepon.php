<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nasabah_buku_telepon".
 *
 * @property int $id
 * @property string $nama
 * @property string $nomor_telepon
 * @property int $id_nasabah
 *
 * @property Nasabah $nasabah
 */
class NasabahBukuTelepon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nasabah_buku_telepon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_nasabah'], 'integer'],
            [['nama'], 'string', 'max' => 100],
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
            'nama' => 'Nama',
            'nomor_telepon' => 'Nomor Telepon',
            'id_nasabah' => 'Id Nasabah',
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
