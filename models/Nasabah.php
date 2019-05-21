<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nasabah".
 *
 * @property int $id
 * @property int $id_akun
 * @property string $nama
 * @property string $alamat
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $jenis_kelamin
 * @property string $nomor_telepon
 * @property string $email
 * @property string $foto_ktp
 * @property string $foto_bersama_ktp
 * @property double $latitude
 * @property double $longitude
 * @property string $access_token
 *
 * @property Akun $akun
 * @property NasabahBukuTelepon[] $nasabahBukuTelepons
 * @property Peminjaman[] $peminjamen
 */
class Nasabah extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nasabah';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_kelamin'], 'required'],
            [['id_akun'], 'integer'],
            [['tanggal_lahir'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['nama', 'alamat', 'tempat_lahir', 'email'], 'string', 'max' => 100],
            [['nomor_telepon'], 'string', 'max' => 20],
            [['foto_ktp'], 'file', 'extensions' => 'jpg, png, jpeg'],
            [['foto_ktp', 'foto_bersama_ktp', 'access_token'], 'string', 'max' => 255],
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
            'id_akun' => 'Jenis Pinjaman',
            'nama' => 'Nama',
            'alamat' => 'Alamat',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'nomor_telepon' => 'Nomor Telepon',
            'email' => 'Email',
            'foto_ktp' => 'Foto Ktp',
            'foto_bersama_ktp' => 'Foto Bersama Ktp',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'access_token' => 'Access Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAkun()
    {
        return $this->hasOne(Akun::className(), ['id' => 'id_akun']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNasabahBukuTelepons()
    {
        return $this->hasMany(NasabahBukuTelepon::className(), ['id_nasabah' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjamen()
    {
        return $this->hasMany(Peminjaman::className(), ['id_nasabah' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjamanOne()
    {
        return $this->hasOne(Peminjaman::className(), ['id_nasabah' => 'id']);
    }

    public function validatePassword($password, $password_hash)
    {
        return Yii::$app->security->validatePassword($password, $password_hash);
    }
}
