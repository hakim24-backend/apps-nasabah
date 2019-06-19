<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pengguna".
 *
 * @property int $id
 * @property string $nama
 * @property string $alamat
 * @property string $jenis_kelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $email
 * @property int $id_akun
 *
 * @property Peminjaman[] $peminjamen
 * @property Pencicilan[] $pencicilans
 * @property Akun $akun
 */
class Pengguna extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pengguna';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_kelamin'], 'required'],
            [['tanggal_lahir'], 'safe'],
            [['id_akun'], 'integer'],
            [['nama', 'alamat', 'email'], 'string', 'max' => 100],
            [['tempat_lahir'], 'string', 'max' => 50],
            [['email'], 'unique'],
            [['id_akun'], 'exist', 'skipOnError' => true, 'targetClass' => Akun::className(), 'targetAttribute' => ['id_akun' => 'id']],
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
            'alamat' => 'Alamat',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'email' => 'Email',
            'id_akun' => 'Role',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjamen()
    {
        return $this->hasMany(Peminjaman::className(), ['id_pengguna' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPencicilans()
    {
        return $this->hasMany(Pencicilan::className(), ['id_pengguna' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAkun()
    {
        return $this->hasOne(Akun::className(), ['id' => 'id_akun']);
    }
}
