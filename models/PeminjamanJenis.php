<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "peminjaman_jenis".
 *
 * @property int $id
 * @property string $jenis_peminjaman
 * @property double $besar_bunga
 *
 * @property Peminjaman[] $peminjamen
 */
class PeminjamanJenis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'peminjaman_jenis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['besar_bunga'], 'number'],
            [['jenis_peminjaman'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_peminjaman' => 'Jenis Peminjaman',
            'besar_bunga' => 'Besar Bunga',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjamen()
    {
        return $this->hasMany(Peminjaman::className(), ['id_jenis_peminjaman' => 'id']);
    }
}
