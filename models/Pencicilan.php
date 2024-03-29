<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pencicilan".
 *
 * @property int $id
 * @property int $id_peminjaman
 * @property int $id_pengguna
 * @property int $id_jenis_pencicilan
 * @property string $tanggal_jatuh_tempo
 * @property double $nominal_cicilan
 * @property string $tanggal_waktu_cicilan
 * @property int $id_status_bayar
 * @property int $periode
 * @property double $nominal_denda_dibayar
 * @property double $nominal_denda_berhenti
 *
 * @property Peminjaman $peminjaman
 * @property Pengguna $pengguna
 * @property PencicilanJenis $jenisPencicilan
 * @property PencicilanStatusBayar $statusBayar
 */
class Pencicilan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pencicilan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_peminjaman', 'id_pengguna', 'id_jenis_pencicilan', 'id_status_bayar', 'periode'], 'integer'],
            [['tanggal_jatuh_tempo', 'tanggal_waktu_cicilan'], 'safe'],
            [['nominal_cicilan'], 'number'],
            [['nominal_denda_dibayar'], 'number'],
            [['nominal_denda_berhenti'], 'number'],
            [['id_peminjaman'], 'exist', 'skipOnError' => true, 'targetClass' => Peminjaman::className(), 'targetAttribute' => ['id_peminjaman' => 'id']],
            [['id_pengguna'], 'exist', 'skipOnError' => true, 'targetClass' => Pengguna::className(), 'targetAttribute' => ['id_pengguna' => 'id']],
            [['id_jenis_pencicilan'], 'exist', 'skipOnError' => true, 'targetClass' => PencicilanJenis::className(), 'targetAttribute' => ['id_jenis_pencicilan' => 'id']],
            [['id_status_bayar'], 'exist', 'skipOnError' => true, 'targetClass' => PencicilanStatusBayar::className(), 'targetAttribute' => ['id_status_bayar' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_peminjaman' => 'Id Peminjaman',
            'id_pengguna' => 'Admin',
            'id_jenis_pencicilan' => 'Jenis Pencicilan',
            'tanggal_jatuh_tempo' => 'Tanggal Jatuh Tempo',
            'nominal_cicilan' => 'Nominal Pembayaran',
            'nominal_denda_dibayar' => 'Denda',
            'nominal_denda_berhenti' => 'Nominal Denda Berhenti',
            'tanggal_waktu_cicilan' => 'Terbayar Pada',
            'id_status_bayar' => 'Status Pembayaran',
            'periode' => 'Periode',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjaman()
    {
        return $this->hasOne(Peminjaman::className(), ['id' => 'id_peminjaman']);
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
    public function getJenisPencicilan()
    {
        return $this->hasOne(PencicilanJenis::className(), ['id' => 'id_jenis_pencicilan']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusBayar()
    {
        return $this->hasOne(PencicilanStatusBayar::className(), ['id' => 'id_status_bayar']);
    }

    public function getTotalCicilan($id_peminjaman)
    {
        $data = Pencicilan::find()->select('count(*) as total')->where(['id_peminjaman'=>$id_peminjaman])->andWhere(['id_status_bayar'=>2])->asArray()->one();
        return $data['total'];
    }

    public function getLunasDipercepat($id_jenis_peminjaman,$totalCicilan,$durasi,$nominal_peminjaman, $besar_pinalti_langsung_lunas, $nominal_cicilan, $id_peminjaman, $nominal_tabungan_ditahan)
    {
        $pencicilan = Pencicilan::find()->where(['id_peminjaman'=>$id_peminjaman])->orderBy(['periode'=>SORT_ASC])->all();
        
        if ($id_jenis_peminjaman == 1) {
            $periode = 0;
            $tertinggal = 0;
            $in_period = 0;
            foreach ($pencicilan as $key => $value) {
                if($value->id_status_bayar == 1){
                    if($value->nominal_cicilan != null){
                        $tertinggal += $nominal_cicilan - $value->nominal_cicilan;
                    } else {
                        $tertinggal += $nominal_cicilan;
                    }
                }
                if(strtotime(date("Y-m-d")) < strtotime($value->tanggal_jatuh_tempo) && $value->id_status_bayar == 1){
                    $periode = $value->periode;
                    if($key>0){
                        if(strtotime($pencicilan[$key-1]->tanggal_jatuh_tempo) > strtotime(date("Y-m-d"))){
                            $in_period = 1;
                            $tertinggal -= $nominal_cicilan;
                        }
                    }
                    break;
                }
            }

            $intervalDurasi = $durasi - $periode + $in_period;
            $sisaCicilan = ($nominal_peminjaman / $durasi) * $intervalDurasi;
            return $tertinggal+($sisaCicilan)+($besar_pinalti_langsung_lunas/100*$sisaCicilan);

        } else {
            $pernah_telat = false;

            foreach ($pencicilan as $key => $value) {
                if($value->id_status_bayar == 1){
                    if(strtotime(date("Y-m-d")) > strtotime($value->tanggal_jatuh_tempo)){
                        $pernah_telat = true;
                        break;
                    }
                } else {
                    if($value->nominal_denda_dibayar > 0){
                        $pernah_telat = true;
                        break;
                    }
                }
            }

            //lunas dipercepat non-jaminan
            $intervalDurasi = $durasi - $totalCicilan;

            if($pernah_telat){
                return $nominal_cicilan * $intervalDurasi;
            } else {
                return $nominal_cicilan * $intervalDurasi - $nominal_tabungan_ditahan;
            }

            
        }
    }
}
