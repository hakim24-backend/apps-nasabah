<?php

namespace app\controllers;

use Yii;
use app\models\Peminjaman;
use app\models\PeminjamanJenis;
use app\models\Nasabah;
use app\models\PeminjamanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Uploadedfile;
use yii\helpers\ArrayHelper;

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
        $model = new Peminjaman();
        $jenis_peminjaman = PeminjamanJenis::find();

        //data nasabah
        $nama = ArrayHelper::map(Nasabah::find()->all(), 'id', 'nama');

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            $dataNasabah = Nasabah::find()->where(['id'=>$model->nama])->one();
            $nomor_kontrak = $post['nomor_kontrak'];
            $nomor_kontrak_tipe = str_replace('/','-',$nomor_kontrak);
            

            //upload foto ktp
            $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
            $images_name_ktp = 'ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
            $pathKtp = 'foto/'.$images_name_ktp;
            if ($imagesKtp->saveAs($pathKtp)) {
                $model->foto_ktp = $images_name_ktp;
            }

            //upload foto ktp bersama
            $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
            $images_name_ktp_2 = 'bersama_ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
            $pathKtp2 = 'foto/'.$images_name_ktp_2;
            if ($imagesKtp2->saveAs($pathKtp2)) {
                $model->foto_bersama_ktp = $images_name_ktp_2;
            }

            $model->id_nasabah = $model->nama;
            $model->nama = $dataNasabah->nama;
            $model->id_jenis_peminjaman = $post['status'];

            if ($post['status'] == 1) {
                $model->jaminan = $post['jaminan'];
            } else {
                $model->jaminan = null;
            }

            $model->nomor_kontrak = $post['nomor_kontrak'];
            $model->id_jenis_durasi = $post['jenis-durasi'];
            $model->tanggal_waktu_pembuatan = date('Y-m-d H:i:s');
            $model->id_status_peminjaman = 1;
            $model->save(false);

            Yii::$app->session->setFlash('success', "Tambah Data Peminjaman Berhasil");
            return $this->redirect('index');
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
            $kode = 'J/';
            $list = Peminjaman::find()->where(['LIKE','nomor_kontrak',$kode])->orderBy(['nomor_kontrak'=> SORT_DESC])->limit(1)->one();
            if ($list == null) {
                $counter = '001';
                $month = date('m');
                $year = date('Y');
            } else {
                $counter = explode('/',$list['nomor_kontrak'])[1];
                $counter = str_pad(intval($counter+1), 3, '0', STR_PAD_LEFT);
                $month = date('m');
                $year = date('Y');
            }
            
            $code = $kode.''.$counter.'/'.$month.'/'.$year;

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
            $kode = 'M/';
            $list = Peminjaman::find()->where(['LIKE','nomor_kontrak',$kode])->orderBy(['nomor_kontrak'=> SORT_DESC])->limit(1)->one();
            if ($list == null) {
                $counter = '001';
                $month = date('m');
                $year = date('Y');
            } else {
                $counter = explode('/',$list['nomor_kontrak'])[1];
                $counter = str_pad(intval($counter+1), 3, '0', STR_PAD_LEFT);
                $month = date('m');
                $year = date('Y');
            }
            
            $code = $kode.''.$counter.'/'.$month.'/'.$year;

            echo '
                <label>Nomor Kontrak</label>
                <input type="text" id="peminjaman-nomor_kontrak" class="form-control" name="nomor_kontrak" maxlength="15" aria-invalid="false" value="'.$code.'" readonly ><br>
            ';

        } elseif ($id_durasi == 2) {
            
            //create nomor kontrak bulanan
            $kode = 'B/';
            $list = Peminjaman::find()->where(['LIKE','nomor_kontrak',$kode])->orderBy(['nomor_kontrak'=> SORT_DESC])->limit(1)->one();
            if ($list == null) {
                $counter = '01';
                $month = date('m');
                $year = date('Y');
            } else {
                $counter = explode('/',$list['nomor_kontrak'])[1];
                $counter = str_pad(intval($counter+1), 2, '0', STR_PAD_LEFT);
                $month = date('m');
                $year = date('Y');
            }
            
            $code = $kode.''.$counter.'/'.$month.'/'.$year;

            echo '
                <label>Nomor Kontrak</label>
                <input type="text" id="peminjaman-nomor_kontrak" class="form-control" name="nomor_kontrak" maxlength="15" aria-invalid="false" value="'.$code.'" readonly ><br>
            ';

        } else {
            echo '

            ';
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

        //data nasabah
        $nama = ArrayHelper::map(Nasabah::find()->all(), 'id', 'nama');

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            $dataNasabah = Nasabah::find()->where(['id'=>$model->id_nasabah])->one();
            $nomor_kontrak = $model->nomor_kontrak;
            $nomor_kontrak_tipe = str_replace('/','-',$nomor_kontrak);

            //foto ktp null and foto bersama ktp null
            if ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') == null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') == null) {

                $model->foto_ktp = $oldKtp;
                $model->foto_bersama_ktp = $oldKtp2;

            //foto ktp not null and foto bersama ktp null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') != null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') == null) {
                
                //upload foto ktp
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = 'ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
                $pathKtp = 'foto/'.$images_name_ktp;
                if ($imagesKtp->saveAs($pathKtp)) {
                    $model->foto_ktp = $images_name_ktp;
                }

                //no update upload foto ktp bersama
                $model->foto_bersama_ktp = $oldKtp2;

            //foto ktp null and foto bersama ktp not null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') == null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') != null) {

                //no update upload foto ktp
                $model->foto_ktp = $oldKtp;

                //upload foto ktp bersama
                $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                $images_name_ktp_2 = 'bersama_ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $images_name_ktp_2;
                }
            
            //foto ktp not null and foto bersama ktp not null
            } else {

                //upload foto ktp
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = 'ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp->extension;
                $pathKtp = 'foto/'.$images_name_ktp;
                if ($imagesKtp->saveAs($pathKtp)) {
                    $model->foto_ktp = $images_name_ktp;
                }

                //upload foto ktp bersama
                $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                $images_name_ktp_2 = 'bersama_ktp-'.$nomor_kontrak_tipe.'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $images_name_ktp_2;
                }
            }

            $model->nama = $dataNasabah->nama;
            $model->id_jenis_peminjaman = $post['status'];

            if ($post['status'] == 1) {
                $model->jaminan = $model->jaminan;
            } else {
                $model->jaminan = null;
            }

            $model->id_jenis_durasi = $post['jenis-durasi'];
            $model->tanggal_waktu_pembuatan = date('Y-m-d H:i:s');
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