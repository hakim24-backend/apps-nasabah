<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "peminjaman".
 *
 * @property int $id
 * @property int $id_nasabah
 * @property int $id_jenis_peminjaman
 * @property string $nomor_kontrak
 * @property string $nama
 * @property string $alamat
 * @property string $nik_ktp
 * @property double $nominal_peminjaman
 * @property int $id_jenis_durasi
 * @property int $durasi
 * @property string $jaminan
 * @property string $foto_ktp
 * @property string $foto_bersama_ktp
 * @property string $tanggal_waktu_pembuatan waktu_pembuatan_data
 * @property int $id_status_peminjaman
 * @property int $id_pengguna
 *
 * @property Nasabah $nasabah
 * @property PeminjamanJenis $jenisPeminjaman
 * @property PeminjamanDurasiJenis $jenisDurasi
 * @property PeminjamanStatus $statusPeminjaman
 * @property Pengguna $pengguna
 * @property Pencicilan[] $pencicilans
 */
class Peminjaman extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'peminjaman';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_nasabah', 'id_jenis_peminjaman', 'id_jenis_durasi', 'durasi', 'id_status_peminjaman', 'id_pengguna','nominal_peminjaman'], 'integer'],
            [['tanggal_waktu_pembuatan'], 'safe'],
            [['nomor_kontrak'], 'string', 'max' => 15],
            [['nama', 'alamat', 'jaminan'], 'string', 'max' => 100],
            [['nik_ktp'], 'string', 'max' => 20],
            [['foto_ktp', 'foto_bersama_ktp'], 'string', 'max' => 255],
            [['nomor_kontrak'], 'unique'],
            [['id_nasabah'], 'exist', 'skipOnError' => true, 'targetClass' => Nasabah::className(), 'targetAttribute' => ['id_nasabah' => 'id']],
            [['id_jenis_peminjaman'], 'exist', 'skipOnError' => true, 'targetClass' => PeminjamanJenis::className(), 'targetAttribute' => ['id_jenis_peminjaman' => 'id']],
            [['id_jenis_durasi'], 'exist', 'skipOnError' => true, 'targetClass' => PeminjamanDurasiJenis::className(), 'targetAttribute' => ['id_jenis_durasi' => 'id']],
            [['id_status_peminjaman'], 'exist', 'skipOnError' => true, 'targetClass' => PeminjamanStatus::className(), 'targetAttribute' => ['id_status_peminjaman' => 'id']],
            [['id_pengguna'], 'exist', 'skipOnError' => true, 'targetClass' => Pengguna::className(), 'targetAttribute' => ['id_pengguna' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_nasabah' => 'Nama',
            'id_jenis_peminjaman' => 'Jenis Peminjaman',
            'nomor_kontrak' => 'Nomor Kontrak',
            'nama' => 'Nama',
            'alamat' => 'Alamat',
            'nik_ktp' => 'NIK KTP',
            'nominal_peminjaman' => 'Nominal Peminjaman',
            'id_jenis_durasi' => 'Jenis Durasi',
            'durasi' => 'Durasi',
            'jaminan' => 'Jaminan',
            'foto_ktp' => 'Foto KTP',
            'foto_bersama_ktp' => 'Foto Bersama KTP',
            'tanggal_waktu_pembuatan' => 'Tanggal Waktu Pembuatan',
            'id_status_peminjaman' => 'Status Peminjaman',
            'id_pengguna' => 'Id Pengguna',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNasabah()
    {
        return $this->hasOne(Nasabah::className(), ['id' => 'id_nasabah']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisPeminjaman()
    {
        return $this->hasOne(PeminjamanJenis::className(), ['id' => 'id_jenis_peminjaman']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisDurasi()
    {
        return $this->hasOne(PeminjamanDurasiJenis::className(), ['id' => 'id_jenis_durasi']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusPeminjaman()
    {
        return $this->hasOne(PeminjamanStatus::className(), ['id' => 'id_status_peminjaman']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengguna()
    {
        return $this->hasOne(Pengguna::className(), ['id' => 'id_pengguna']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPencicilans()
    {
        return $this->hasMany(Pencicilan::className(), ['id_peminjaman' => 'id']);
    }

    public function getDueDate($create_date, $paid_count){
        return date("Y-m-d",strtotime(date("Y-m-d", strtotime($create_date)) . " +" . ($paid_count + 1) . " month"));
    }

    public function getDenda($due_date, $payment_amount, $late_penalty_percent){

        $late_day = (strtotime(date("Y-m-d")) - strtotime($due_date)) / (60 * 60 * 24);
        if ($late_day < 1){
            $late_day = 0;
        }
        
        return $late_day * $payment_amount * $late_penalty_percent / 100 / 30;
    }
}
