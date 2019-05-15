<?php

namespace app\controllers;

use Yii;
use app\models\Nasabah;
use app\models\Akun;
use app\models\NasabahSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Uploadedfile;

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
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Nasabah model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Nasabah();
        $akun = new Akun();
        $password = 123456;

        if ($model->load(Yii::$app->request->post())) {
            $akun->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
            $akun->access_token = Yii::$app->getSecurity()->generateRandomString();
            $akun->tanggal_waktu_pembuatan = date('Y-m-d H:i:s');
            $akun->id_status_akun = 1;
            $akun->id_jenis_akun = 2;
            $saveAkun = $akun->save(false);

            if ($saveAkun) {
                $model->id_akun = $akun->id;

                //upload foto ktp
                $imagesKtp = Uploadedfile::getInstance($model,'foto_ktp');
                $images_name_ktp = rand(1,4000).'.'.$imagesKtp->extension;
                $pathKtp = 'foto/'.$images_name_ktp;
                if ($imagesKtp->saveAs($pathKtp)) {
                    $model->foto_ktp = $pathKtp;
                }

                //upload foto ktp bersama
                $imagesKtp2 = Uploadedfile::getInstance($model,'foto_bersama_ktp');
                $images_name_ktp_2 = rand(1,4000).'.'.$imagesKtp2->extension;
                $pathKtp2 = 'foto/'.$images_name_ktp_2;
                if ($imagesKtp2->saveAs($pathKtp2)) {
                    $model->foto_bersama_ktp = $pathKtp2;
                }

                $model->save(false);
            }

            return $this->redirect('index');
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
