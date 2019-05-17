<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "peminjaman_jenis".
 *
 * @property int $id
 * @property string $jenis_peminjaman
 * @property double $besar_bunga
 * @property double $besar_admin
 * @property double $besar_tabungan_ditahan
 * @property double $besar_denda
 * @property double $besar_pinalti_langsung_lunas
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
            [['besar_bunga', 'besar_admin', 'besar_tabungan_ditahan', 'besar_denda', 'besar_pinalti_langsung_lunas'], 'number'],
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
            'besar_admin' => 'Besar Admin',
            'besar_tabungan_ditahan' => 'Besar Tabungan Ditahan',
            'besar_denda' => 'Besar Denda',
            'besar_pinalti_langsung_lunas' => 'Besar Pinalti Langsung Lunas',
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
