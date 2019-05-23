<?php

namespace app\controllers;

use Yii;
use app\models\Peminjaman;
use app\models\Akun;
use app\models\Pencicilan;
use app\models\PeminjamanJenis;
use app\models\Nasabah;
use app\models\PeminjamanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Uploadedfile;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use yii\helpers\Html;

/**
 * PeminjamanController implements the CRUD actions for Peminjaman model.
 */
class PeminjamanController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Peminjaman models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PeminjamanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->andWhere(['id_status_peminjaman' => 1]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Peminjaman model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Peminjaman model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        date_default_timezone_set("Asia/Jakarta");
        $model = new Peminjaman();

        //join nasabah with akun
        $data = Nasabah::find()
        ->leftJoin('Akun', 'Akun.id = Nasabah.id_akun')
        ->where(['Akun.id_status_akun'=>1])
        ->asArray()
        ->all();

        //data nasabah
        $nama = ArrayHelper::map($data, 'id', 'email');

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $dataNasabah = Nasabah::find()->where(['id'=>$model->nama])->one();
                $nomor_kontrak = $post['nomor_kontrak'];
                $nomor_kontrak_tipe = str_replace('/','-',$nomor_kontrak);
                

                //upload foto optional
                $imagesKtp = Uploadedfile::getInstance($model,'foto_optional');

                if ($imagesKtp != null) {
                    $images_name_ktp = 'ktp-optional-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
                    $pathKtp = 'foto/'.$images_name_ktp;
                    if ($imagesKtp->saveAs($pathKtp)) {
                        $model->foto_optional = $images_name_ktp;
                    }
                } else {
                    $model->foto_optional = null;
                }

                $model->id_nasabah = $model->nama;
                $model->nama = $dataNasabah->nama;
                $model->id_jenis_peminjaman = $post['status'];
                // $model->save(false);

                if ($post['status'] == 1) {
                    $model->jaminan = $post['jaminan'];
                    $jenisPeminjaman = PeminjamanJenis::find()->where(['id'=>$model->id_jenis_peminjaman])->one();

                    //nominal admin
                    $adminNominal = $model->nominal_peminjaman*$jenisPeminjaman->besar_admin/100;

                    //nominal tabungan ditahan
                    $tabunganDitahan = $model->nominal_peminjaman*$jenisPeminjaman->besar_tabungan_ditahan/100;

                    //nominal pencicilan
                    $cicilan = (($model->nominal_peminjaman*$model->durasi*$jenisPeminjaman->besar_bunga/100)+($model->nominal_peminjaman))/$model->durasi;

                    $model->nominal_admin = $adminNominal;
                    $model->nominal_tabungan_ditahan = $tabunganDitahan;
                    $model->nominal_pencicilan = $cicilan;
                } else {
                    $model->jaminan = null;
                    $jenisPeminjaman = PeminjamanJenis::find()->where(['id'=>$model->id_jenis_peminjaman])->one();

                    //nominal admin
                    $adminNominal = $model->nominal_peminjaman*$jenisPeminjaman->besar_admin/100;

                    //nominal tabungan ditahan
                    $tabunganDitahan = $model->nominal_peminjaman*$jenisPeminjaman->besar_tabungan_ditahan/100;

                    //nominal pencicilan
                    $cicilan = (($model->nominal_peminjaman*$model->durasi*$jenisPeminjaman->besar_bunga/100)+($model->nominal_peminjaman))/$model->durasi;

                    $model->nominal_admin = $adminNominal;
                    $model->nominal_tabungan_ditahan = $tabunganDitahan;
                    $model->nominal_pencicilan = $cicilan;
                }

                $model->alamat = $post['alamat'];
                $model->foto_ktp = $post['foto_ktp'];
                $model->foto_bersama_ktp = $post['foto_ktp_2'];
                $model->nomor_kontrak = $post['nomor_kontrak'];
                $model->id_jenis_durasi = $post['jenis-durasi'];
                $model->tanggal_waktu_pembuatan = date('Y-m-d H:i:s');
                $model->id_status_peminjaman = 1;
                $savePeminjaman = $model->save(false);

                if ($savePeminjaman) {

                    for($i=0; $i < $model->durasi; $i++) {
                        $cicilan = new Pencicilan();
                        $cicilan->id_peminjaman = $model->id;
                        $cicilan->id_jenis_pencicilan = 1;
                        $cicilan->periode = ($i + 1);
                        $cicilan->id_status_bayar = 1;
                        $cicilan->tanggal_jatuh_tempo = Peminjaman::getDueDate($model->id_jenis_durasi, $model->tanggal_waktu_pembuatan, $i);
                        $cicilan->save(false);   
                    }

                    $transaction->commit();
                    return $this->render('detail', [
                        'model' => $model,
                    ]);
                }

            } catch(\Exception $e) {

                $transaction->rollback();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect('index');

            }
        }

        return $this->render('create', [
            'model' => $model,
            'nama' => $nama
        ]);
    }

    public function actionGetStatus($id)
    {
        if ($id == 1) {

            //create nomor kontrak
            $data = Peminjaman::find()->where(['id_jenis_peminjaman'=>1])->orderBy(['id'=>SORT_DESC])->limit(1)->one();

            if ($data == null) {
                $digitFront = '0001';
                $month = date('m');
                $year = date('Y');
            } else {
                $digitFront = explode('/',$data['nomor_kontrak'])[0];
                $digitFront = str_pad(intval($digitFront+1), 4, '0', STR_PAD_LEFT);
                $month = date('m');
                $year = date('Y');
            }

            $code = $digitFront.'/'.$month.'/'.$year;

            echo '
                <label>Nomor Kontrak</label>
                <input type="text" id="peminjaman-nomor_kontrak" class="form-control" name="nomor_kontrak" maxlength="15" aria-invalid="false" value="'.$code.'" readonly ><br>

                <label>Jaminan</label>
                <input type="text" id="peminjaman-jaminan" class="form-control" name="jaminan" maxlength="100" aria-invalid="false" required=""><br>
            ';
        } else {
            echo '

            ';
        }
    }

    public function actionGetStatusUpdate($id, $model_id)
    {
        if ($id == 1) {
            $data = Peminjaman::find()->where(['id'=>$model_id])->one();
            echo '
                <div id="jaminan-update">
                    <label>Jaminan</label>
                    <input type="text" id="peminjaman-jaminan" class="form-control" name="jaminan" maxlength="100" aria-invalid="false" value="'.$data->jaminan.'"><br>
                </div>
                
            ';
        } else {
            echo '

            ';
        }
    }

    public function actionGetDurasi($id_durasi)
    {
        if ($id_durasi == 1) {
            
            //create nomor kontrak mingguan
            $data = Peminjaman::find()->where(['id_jenis_peminjaman'=>2])->andWhere(['id_jenis_durasi'=>1])->orderBy(['id'=>SORT_DESC])->limit(1)->one();

            if ($data == null) {
                $code = '00001';
            } else {
                $code = $data['nomor_kontrak'];
                $code = str_pad(intval($code+1), 5, '0', STR_PAD_LEFT);
            }

            echo '
                <label>Nomor Kontrak</label>
                <input type="text" id="peminjaman-nomor_kontrak" class="form-control" name="nomor_kontrak" maxlength="15" aria-invalid="false" value="'.$code.'" readonly ><br>
            ';

        } elseif ($id_durasi == 2) {
            
            //create nomor kontrak bulanan
            $data = Peminjaman::find()->where(['id_jenis_peminjaman'=>2])->andWhere(['id_jenis_durasi'=>2])->orderBy(['id'=>SORT_DESC])->limit(1)->one();

            if ($data == null) {
                $code = '0001';
            } else {
                $code = $data['nomor_kontrak'];
                $code = str_pad(intval($code+1), 4, '0', STR_PAD_LEFT);
            }

            echo '
                <label>Nomor Kontrak</label>
                <input type="text" id="peminjaman-nomor_kontrak" class="form-control" name="nomor_kontrak" maxlength="15" aria-invalid="false" value="'.$code.'" readonly ><br>
            ';

        } else {
            echo '

            ';
        }
    }

    public function actionGetNasabah($id)
    {
        $data = Nasabah::find()->where(['id'=>$id])->one();

        if ($data) {
            echo '
            <input type="hidden" id="alamat" class="form-control" name="alamat" maxlength="15" aria-invalid="false" value="'.$data->alamat.'" readonly ><br>

            <input type="hidden" id="foto_ktp" class="form-control" name="foto_ktp" maxlength="15" aria-invalid="false" value="'.$data->foto_ktp.'" readonly ><br>

            <input type="hidden" id="foto_ktp_2" class="form-control" name="foto_ktp_2" maxlength="15" aria-invalid="false" value="'.$data->foto_bersama_ktp.'" readonly ><br>
            ';
        } else {
            echo '';
        }
    }

    /**
     * Updates an existing Peminjaman model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldKtp = $model->foto_ktp;
        $oldKtp2 = $model->foto_bersama_ktp;
        $oldKtp3 = $model->foto_optional;

        //data nasabah
        $nama = ArrayHelper::map(Nasabah::find()->all(), 'id', 'nama');

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            $dataNasabah = Nasabah::find()->where(['id'=>$model->id_nasabah])->one();
            $nomor_kontrak = $model->nomor_kontrak;
            $nomor_kontrak_tipe = str_replace('/','-',$nomor_kontrak);

            //1 foto ktp null, foto bersama ktp null and foto optional null
            if ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') == null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') == null && $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional') == null) {

                $model->foto_ktp = $oldKtp;
                $model->foto_bersama_ktp = $oldKtp2;
                $model->foto_optional = $oldKtp3;

            //2 foto ktp not null, foto bersama ktp null and foto_optional null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') != null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') == null && $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional') == null) {
                
                //upload foto ktp
                unlink('foto/'.$oldKtp);
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = 'ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
                $pathKtp = 'foto/'.$images_name_ktp;
                if ($imagesKtp->saveAs($pathKtp)) {
                    $model->foto_ktp = $images_name_ktp;
                }

                //no update upload foto ktp bersama
                $model->foto_bersama_ktp = $oldKtp2;

                //no update upload foto optional
                $model->foto_optional = $oldKtp3;

            //3 foto ktp null, foto bersama ktp not null and foto optional null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') == null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') != null && $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional') == null) {

                //no update upload foto ktp
                $model->foto_ktp = $oldKtp;

                //upload foto ktp bersama
                unlink('foto/'.$oldKtp2);
                $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                $images_name_ktp_2 = 'bersama_ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $images_name_ktp_2;
                }

                //no update upload foto optional
                $model->foto_optional = $oldKtp3;
            
            //4 foto ktp not null, foto bersama ktp null and foto optional not null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') == null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') == null && $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional') != null) {
                
                //no update upload foto ktp
                $model->foto_ktp = $oldKtp;

                //no update upload foto ktp bersama
                $model->foto_bersama_ktp = $oldKtp2;

                //upload foto optional
                unlink('foto/'.$oldKtp3);
                $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional');
                $images_name_ktp_3 = 'ktp-optional-'.$nomor_kontrak_tipe.'.'.$imagesKtp3->extension;
                $pathKtp3 = 'foto/'.$images_name_ktp_3;
                if ($imagesKtp3->saveAs($pathKtp3)) {
                    $model->foto_optional = $images_name_ktp_3;
                }

            //5 foto ktp not null, foto bersama ktp not null and foto optional null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') != null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') != null && $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional') == null) {
                
                //upload foto ktp
                unlink('foto/'.$oldKtp);
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = 'ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
                $pathKtp = 'foto/'.$images_name_ktp;
                if ($imagesKtp->saveAs($pathKtp)) {
                    $model->foto_ktp = $images_name_ktp;
                }

                //upload foto ktp bersama
                unlink('foto/'.$oldKtp2);
                $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                $images_name_ktp_2 = 'bersama_ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $images_name_ktp_2;
                }

                //no update upload foto optional
                $model->foto_optional = $oldKtp3;

            //6 foto ktp null, foto bersama ktp not null and foto optional not null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') == null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') != null && $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional') != null) {
                
                //no update upload foto ktp
                $model->foto_ktp = $oldKtp;

                //upload foto ktp bersama
                unlink('foto/'.$oldKtp2);
                $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                $images_name_ktp_2 = 'bersama_ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $images_name_ktp_2;
                }

                //upload foto optional
                unlink('foto/'.$oldKtp3);
                $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional');
                $images_name_ktp_3 = 'ktp-optional-'.$nomor_kontrak_tipe.'.'.$imagesKtp3->extension;
                $pathKtp3 = 'foto/'.$images_name_ktp_3;
                if ($imagesKtp3->saveAs($pathKtp3)) {
                    $model->foto_optional = $images_name_ktp_3;
                }

            //7 foto ktp not null, foto bersama ktp null and foto optional not null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') != null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') == null && $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional') != null) {
                
                //upload foto ktp
                unlink('foto/'.$oldKtp);
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = 'ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
                $pathKtp = 'foto/'.$images_name_ktp;
                if ($imagesKtp->saveAs($pathKtp)) {
                    $model->foto_ktp = $images_name_ktp;
                }

                //upload foto optional
                unlink('foto/'.$oldKtp3);
                $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional');
                $images_name_ktp_3 = 'ktp-optional-'.$nomor_kontrak_tipe.'.'.$imagesKtp3->extension;
                $pathKtp3 = 'foto/'.$images_name_ktp_3;
                if ($imagesKtp3->saveAs($pathKtp3)) {
                    $model->foto_optional = $images_name_ktp_3;
                }

                //no update upload foto ktp bersama
                $model->foto_bersama_ktp = $oldKtp2;

            } else {

                //upload foto ktp
                unlink('foto/'.$oldKtp);
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = 'ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
                $pathKtp = 'foto/'.$images_name_ktp;
                if ($imagesKtp->saveAs($pathKtp)) {
                    $model->foto_ktp = $images_name_ktp;
                }

                //upload foto ktp bersama
                unlink('foto/'.$oldKtp2);
                $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                $images_name_ktp_2 = 'bersama_ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $images_name_ktp_2;
                }

                //upload foto optional
                unlink('foto/'.$oldKtp3);
                $imagesKtp3 = Uploadedfile::getInstance($model,'foto_optional');
                $images_name_ktp_3 = 'ktp-optional-'.$nomor_kontrak_tipe.'.'.$imagesKtp3->extension;
                $pathKtp3 = 'foto/'.$images_name_ktp_3;
                if ($imagesKtp3->saveAs($pathKtp3)) {
                    $model->foto_optional = $images_name_ktp_3;
                }
            }

            if ($model->id_jenis_peminjaman == 1) {
                $jenisPeminjaman = PeminjamanJenis::find()->where(['id'=>$model->id_jenis_peminjaman])->one();

                //nominal admin
                $adminNominal = $model->nominal_peminjaman*$jenisPeminjaman->besar_admin/100;

                //nominal tabungan ditahan
                $tabunganDitahan = $model->nominal_peminjaman*$jenisPeminjaman->besar_tabungan_ditahan/100;

                //nominal pencicilan
                $cicilan = (($model->nominal_peminjaman*$model->durasi*$jenisPeminjaman->besar_bunga/100)+($model->nominal_peminjaman))/$model->durasi;

                $model->nominal_admin = $adminNominal;
                $model->nominal_tabungan_ditahan = $tabunganDitahan;
                $model->nominal_pencicilan = $cicilan;
            } else {
                $model->jaminan = null;
                $jenisPeminjaman = PeminjamanJenis::find()->where(['id'=>$model->id_jenis_peminjaman])->one();

                //nominal admin
                $adminNominal = $model->nominal_peminjaman*$jenisPeminjaman->besar_admin/100;

                //nominal tabungan ditahan
                $tabunganDitahan = $model->nominal_peminjaman*$jenisPeminjaman->besar_tabungan_ditahan/100;

                //nominal pencicilan
                $cicilan = (($model->nominal_peminjaman*$model->durasi*$jenisPeminjaman->besar_bunga/100)+($model->nominal_peminjaman))/$model->durasi;

                $model->nominal_admin = $adminNominal;
                $model->nominal_tabungan_ditahan = $tabunganDitahan;
                $model->nominal_pencicilan = $cicilan;
            }

            $model->nama = $dataNasabah->nama;
            $model->id_jenis_durasi = $post['jenis-durasi'];
            $model->id_status_peminjaman = $post['peminjaman'];
            $model->save(false);

            Yii::$app->session->setFlash('success', "Update Data Peminjaman Berhasil");
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
            'nama' => $nama,
        ]);
    }

    /**
     * Deletes an existing Peminjaman model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = Peminjaman::find()->where(['id'=>$id])->one();

        if(file_exists('foto/'.$model->foto_ktp) && file_exists('foto/'.$model->foto_bersama_ktp))
        {
            unlink('foto/'.$model->foto_ktp);
            unlink('foto/'.$model->foto_bersama_ktp);
            $model->delete();
        } else {
            $model->delete();
        }

        Yii::$app->session->setFlash('success', "Hapus Data Peminjaman Berhasil");
        return $this->redirect(['index']);
    }

    /**
     * Finds the Peminjaman model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Peminjaman the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Peminjaman::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
