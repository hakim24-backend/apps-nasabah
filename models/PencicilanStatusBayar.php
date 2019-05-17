<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pencicilan_status_bayar".
 *
 * @property int $id
 * @property string $status_bayar
 *
 * @property Pencicilan[] $pencicilans
 * @property PencicilanDipercepat[] $pencicilanDipercepats
 */
class PencicilanStatusBayar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pencicilan_status_bayar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_bayar'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_bayar' => 'Status Bayar',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPencicilans()
    {
        return $this->hasMany(Pencicilan::className(), ['id_status_bayar' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPencicilanDipercepats()
    {
        return $this->hasMany(PencicilanDipercepat::className(), ['id_status_bayar' => 'id']);
    }
}
