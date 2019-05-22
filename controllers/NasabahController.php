<?php

namespace app\controllers;

use Yii;
use app\models\Nasabah;
use app\models\Peminjaman;
use app\models\Akun;
use app\models\NasabahBukuTelepon;
use app\models\NasabahBukuTeleponSearch;
use app\models\NasabahSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Uploadedfile;
use yii\data\ActiveDataProvider;

/**
 * NasabahController implements the CRUD actions for Nasabah model.
 */
class NasabahController extends Controller
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
     * Lists all Nasabah models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NasabahSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Nasabah model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionView($id)
    // {
    //     return $this->renderAjax('view', [
    //         'model' => $this->findModel($id),
    //     ]);
    // }

    public function actionView($id)
    {
        $model = Nasabah::find()->where(['id'=>$id])->one();
        $akun = Akun::find()->where(['id'=>$model->id_akun])->one();

        return $this->render('view', [
            'model' => $model,
            'akun' => $akun
        ]);
    }

    public function actionPhone($id)
    {
        $searchModel = new NasabahBukuTeleponSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['id_nasabah' => $id]);

        // $dataProvider = new ActiveDataProvider([
        //     'query' => NasabahBukuTelepon::find()
        //               ->where(['id_nasabah' => $id])
        // ]);

        return $this->render('phone', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMonitor($id)
    {
        $model = Nasabah::find()->where(['id'=>$id])->one();

        return $this->render('monitor', [
            'model' => $model,
        ]);
    }

    public function actionAktifAkun($id)
    {
        $model = Nasabah::find()->where(['id'=>$id])->one();
        $akun = Akun::find()->where(['id'=>$model->id_akun])->one();
        $akun->id_status_akun = 1;
        $akun->save(false);

        $email = \Yii::$app->mailer->compose('active')
                                    ->setTo($model->email)
                                    ->setFrom(['mamorasoft.firebase@gmail.com'])
                                    ->setSubject('Akun Telah Aktif')
                                    ->send();

        Yii::$app->session->setFlash('success', "Aktifkan Akun Nasabah Berhasil");
        return $this->redirect(['nasabah/index']);
    }

    public function actionNonAktifAkun($id)
    {
        $model = Nasabah::find()->where(['id'=>$id])->one();
        $akun = Akun::find()->where(['id'=>$model->id_akun])->one();
        $akun->id_status_akun = 2;
        $akun->save(false);

        Yii::$app->session->setFlash('success', "Non-Aktifkan Akun Nasabah Berhasil");
        return $this->redirect(['nasabah/index']);
    }

    /**
     * Creates a new Nasabah model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        date_default_timezone_set("Asia/Jakarta");
        $model = new Nasabah();
        $akun = new Akun();
        $password = 123456;

        if ($model->load(Yii::$app->request->post())) {

            $transaction = \Yii::$app->db->beginTransaction();

            try {

                $akun->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
                $akun->access_token = Yii::$app->getSecurity()->generateRandomString();
                $akun->tanggal_waktu_pembuatan = date('Y-m-d H:i:s');
                $akun->id_status_akun = 1;
                $akun->id_jenis_akun = 2;
                $saveAkun = $akun->save(false);

                if ($saveAkun) {
                    $model->id_akun = $akun->id;
                    $model->save(false);

                    //upload foto ktp
                    $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                    $images_name_ktp = 'ktp-'.$model->id.'-'.time().'.'.$imagesKtp->extension;
                    $pathKtp = 'foto/'.$images_name_ktp;
                    if ($imagesKtp->saveAs($pathKtp)) {
                        $model->foto_ktp = $images_name_ktp;
                    }

                    //upload foto ktp bersama
                    $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                    $images_name_ktp_2 = 'bersama_ktp-'.$model->id.'-'.time().'.'.$imagesKtp->extension;
                    $pathKtp2 = 'foto/'.$images_name_ktp_2;
                    if ($imagesKtp2->saveAs($pathKtp2)) {
                        $model->foto_bersama_ktp = $images_name_ktp_2;
                    }

                    $model->save(false);
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', "Tambah Data Nasabah Berhasil");
                    return $this->redirect('index');
                }

            } catch(\Exception $e) {

                $transaction->rollback();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect('index');

            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Nasabah model.
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

        if ($model->load(Yii::$app->request->post())) {

            //foto ktp null and foto bersama ktp null
            if ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') == null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') == null) {

                $model->foto_ktp = $oldKtp;
                $model->foto_bersama_ktp = $oldKtp2;

            //foto ktp not null and foto bersama ktp null
            } elseif ($imagesKtp = Uploadedfile::getInstance($model,'foto_ktp') != null && $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp') == null) {
                
                //upload foto ktp
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = 'ktp-'.$model->id.'-'.time().'.'.$imagesKtp->extension;
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
                $images_name_ktp_2 = 'bersama_ktp-'.$model->id.'-'.time().'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $images_name_ktp_2;
                }
            
            //foto ktp not null and foto bersama ktp not null
            } else {

                //upload foto ktp
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = 'ktp-'.$model->id.'-'.time().'.'.$imagesKtp->extension;
                $pathKtp = 'foto/'.$images_name_ktp;
                if ($imagesKtp->saveAs($pathKtp)) {
                    $model->foto_ktp = $images_name_ktp;
                }

                //upload foto ktp bersama
                $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                $images_name_ktp_2 = 'bersama_ktp-'.$model->id.'-'.time().'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $images_name_ktp_2;
                }
            }
            
            $model->save(false);
            Yii::$app->session->setFlash('success', "Update Data Nasabah Berhasil");
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Nasabah model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = Nasabah::find()->where(['id'=>$id])->one();
        $peminjaman = Peminjaman::find()->where(['id_nasabah'=>$model->id])->one();

        if ($peminjaman) {
            Yii::$app->session->setFlash('error', "Nasabah sedang melakukan transaksi peminjaman");
            return $this->redirect(['index']);
        } else {
            if(file_exists('foto/'.$model->foto_ktp) && file_exists('foto/'.$model->foto_bersama_ktp))
            {
                unlink('foto/'.$model->foto_ktp);
                unlink('foto/'.$model->foto_bersama_ktp);
                $model->delete();
            } else {
                $model->delete();
            }

            Yii::$app->session->setFlash('success', "Hapus Data Nasabah Berhasil");
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Nasabah model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nasabah the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Nasabah::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
