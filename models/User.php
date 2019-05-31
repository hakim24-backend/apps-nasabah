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
 * @property string $password_hash
 * @property int $id_akun
 *
 * @property Peminjaman[] $peminjamen
 * @property Pencicilan[] $pencicilans
 * @property Akun $akun
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
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
            [['tanggal_lahir'], 'safe'],
            [['id_akun'], 'integer'],
            [['nama', 'alamat', 'email'], 'string', 'max' => 100],
            [['jenis_kelamin'], 'string', 'max' => 15],
            [['tempat_lahir'], 'string', 'max' => 50],
            [['password_hash'], 'string', 'max' => 255],
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
            'password_hash' => 'Password Hash',
            'id_akun' => 'Id Akun',
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

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username]);
    }

    public function validatePassword($password, $username)
    {
        $pengguna = Pengguna::find()->where(['email'=>$username])->one();
        // var_dump($pengguna);die();
        $akun = Akun::find()->where(['id'=>$pengguna->id_akun])->one();
        
        return Yii::$app->security->validatePassword($password, $akun->password_hash);
    }

}
