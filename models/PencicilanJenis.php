<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pencicilan_jenis".
 *
 * @property int $id
 * @property string $jenis_pencicilan
 *
 * @property Pencicilan[] $pencicilans
 */
class PencicilanJenis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pencicilan_jenis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_pencicilan'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_pencicilan' => 'Jenis Pencicilan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPencicilans()
    {
        return $this->hasMany(Pencicilan::className(), ['id_jenis_pencicilan' => 'id']);
    }
}
