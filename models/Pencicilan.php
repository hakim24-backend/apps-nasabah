<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pencicilan".
 *
 * @property int $id
 * @property int $id_peminjaman
 * @property double $nominal_cicilan
 * @property string $tanggal_waktu_cicilan
 * @property int $id_pengguna
 *
 * @property Peminjaman $peminjaman
 * @property Pengguna $pengguna
 */
class Pencicilan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pencicilan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_peminjaman', 'id_pengguna'], 'integer'],
            [['nominal_cicilan'], 'number'],
            [['tanggal_waktu_cicilan'], 'safe'],
            [['id_peminjaman'], 'exist', 'skipOnError' => true, 'targetClass' => Peminjaman::className(), 'targetAttribute' => ['id_peminjaman' => 'id']],
            [['id_pengguna'], 'exist', 'skipOnError' => true, 'targetClass' => Pengguna::className(), 'targetAttribute' => ['id_pengguna' => 'id']],
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
            'nominal_cicilan' => 'Nominal Cicilan',
            'tanggal_waktu_cicilan' => 'Tanggal Waktu Cicilan',
            'id_pengguna' => 'Id Pengguna',
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
    public function getPengguna()
    {
        return $this->hasOne(Pengguna::className(), ['id' => 'id_pengguna']);
    }
}
