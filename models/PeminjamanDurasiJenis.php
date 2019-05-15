<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "peminjaman_durasi_jenis".
 *
 * @property int $id
 * @property string $durasi_peminjaman
 *
 * @property Peminjaman[] $peminjamen
 */
class PeminjamanDurasiJenis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'peminjaman_durasi_jenis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['durasi_peminjaman'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'durasi_peminjaman' => 'Durasi Peminjaman',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjamen()
    {
        return $this->hasMany(Peminjaman::className(), ['id_jenis_durasi' => 'id']);
    }
}
