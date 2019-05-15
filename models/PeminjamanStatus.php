<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "peminjaman_status".
 *
 * @property int $id
 * @property string $status_peminjaman
 *
 * @property Peminjaman[] $peminjamen
 */
class PeminjamanStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'peminjaman_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_peminjaman'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_peminjaman' => 'Status Peminjaman',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjamen()
    {
        return $this->hasMany(Peminjaman::className(), ['id_status_peminjaman' => 'id']);
    }
}
