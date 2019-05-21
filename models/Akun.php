<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "akun".
 *
 * @property int $id
 * @property string $password_hash
 * @property string $access_token
 * @property string $tanggal_waktu_pembuatan
 * @property int $id_status_akun
 * @property int $id_jenis_akun
 *
 * @property AkunStatus $statusAkun
 * @property AkunJenis $jenisAkun
 * @property Nasabah[] $nasabahs
 * @property Pengguna[] $penggunas
 */
class Akun extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'akun';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tanggal_waktu_pembuatan'], 'safe'],
            [['id_status_akun', 'id_jenis_akun'], 'integer'],
            [['password_hash', 'access_token'], 'string', 'max' => 255],
            [['id_status_akun'], 'exist', 'skipOnError' => true, 'targetClass' => AkunStatus::className(), 'targetAttribute' => ['id_status_akun' => 'id']],
            [['id_jenis_akun'], 'exist', 'skipOnError' => true, 'targetClass' => AkunJenis::className(), 'targetAttribute' => ['id_jenis_akun' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'password_hash' => 'Password Hash',
            'access_token' => 'Access Token',
            'tanggal_waktu_pembuatan' => 'Tanggal Waktu Pembuatan',
            'id_status_akun' => 'Id Status Akun',
            'id_jenis_akun' => 'Id Jenis Akun',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusAkun()
    {
        return $this->hasOne(AkunStatus::className(), ['id' => 'id_status_akun']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisAkun()
    {
        return $this->hasOne(AkunJenis::className(), ['id' => 'id_jenis_akun']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNasabahs()
    {
        return $this->hasMany(Nasabah::className(), ['id_akun' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenggunas()
    {
        return $this->hasMany(Pengguna::className(), ['id_akun' => 'id']);
    }

    public function createAkun($akun, $password, $id_jenis_akun){


        $akun->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        $akun->access_token = Yii::$app->getSecurity()->generateRandomString();
        $akun->tanggal_waktu_pembuatan = date('Y-m-d H:i:s');
        $akun->id_status_akun = 2;
        $akun->id_jenis_akun = $id_jenis_akun;
        $akun->save(false);

        return $akun->id;
    }
}
