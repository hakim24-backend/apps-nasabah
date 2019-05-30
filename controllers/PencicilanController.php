<?php

namespace app\controllers;

use Yii;
use app\models\Pencicilan;
use app\models\Peminjaman;
use app\models\PeminjamanJenis;
use app\models\PencicilanSearch;
use app\models\PeminjamanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PencicilanController implements the CRUD actions for Pencicilan model.
 */
class PencicilanController extends Controller
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
     * Lists all Pencicilan models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PeminjamanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pencicilan model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'id' => $id
        ]);
    }

    public function actionViewCicilan($id)
    {
        $model = Peminjaman::find()->where(['id'=>$id])->one();

        return $this->render('view_cicilan', [
            'model' => $model
        ]);
    }

    public function actionCicilan($id)
    {
        $searchModel = new PencicilanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['id_peminjaman' => $id]);

        $info = Peminjaman::find()->where(['id'=>$id])->one();
        $jenisPeminjaman = PeminjamanJenis::find()->where(['id'=>$info->id_jenis_peminjaman])->one();
        $cicilanDenda = Pencicilan::find()->where(['id_peminjaman'=>$id])->one();
        $data = Pencicilan::find()->select('count(id_peminjaman) as total')->groupBy('id_peminjaman')->where(['id_peminjaman'=>$id])->andWhere(['id_status_bayar'=>2])->asArray()->all();
        $cicilan = [];
        foreach ($data as $key => $value) {
            $cicilan = (int)$value['total'];
        }
        $totalCicilan = json_encode($cicilan);

        return $this->render('cicilan', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id' => $id,
            'totalCicilan' => $totalCicilan,
            'info' => $info
        ]);
    }

    /**
     * Creates a new Pencicilan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        date_default_timezone_set("Asia/Jakarta");

        $model = Pencicilan::find()->where(['id'=>$id])->one();
        $peminjaman = Peminjaman::find()->where(['id'=>$model->id_peminjaman])->one();
        $info = Peminjaman::find()->where(['id'=>$model->id_peminjaman])->one();
        $jenisPeminjaman = PeminjamanJenis::find()->where(['id'=>$info->id_jenis_peminjaman])->one();
        $cicilanDenda = Pencicilan::find()->where(['id'=>$id])->one();

        $totalCicilan = Pencicilan::getTotalCicilan($model->id_peminjaman);

        //lunas dipercepat jaminan
        $rumus = Pencicilan::getLunasDipercepat($peminjaman->id_jenis_peminjaman, $totalCicilan, $peminjaman->durasi, $peminjaman->nominal_peminjaman, $jenisPeminjaman->besar_pinalti_langsung_lunas, $peminjaman->nominal_pencicilan, $peminjaman->id, $peminjaman->nominal_tabungan_ditahan);
        $denda = Peminjaman::getDenda($cicilanDenda->tanggal_jatuh_tempo, $info->nominal_pencicilan, $jenisPeminjaman->besar_denda);

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();

            if ($post['cicilan'] == 2) {
                
                //update status lunas
                $peminjaman->id_status_peminjaman = 2;
                $peminjaman->save(false);

                $dataCicilan = Pencicilan::find()->where(['id_peminjaman'=>$peminjaman->id])->all();

                foreach ($dataCicilan as $key => $value) {
                    $value->id_status_bayar = 2;
                    $value->save(false);
                }

                $model->tanggal_waktu_cicilan = date('Y-m-d H:i:s');
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->nominal_denda_dibayar = $denda;
                $model->save(false);

                Yii::$app->session->setFlash('success', "Tambah Data Cicilan Nasabah Berhasil");
                return $this->redirect(['pencicilan/index']);
            } else {
                $model->id_status_bayar = 2;
                $model->tanggal_waktu_cicilan = date('Y-m-d H:i:s');
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->nominal_denda_dibayar = $denda;
                $model->save(false);

                $dataLunas = Pencicilan::find()->where(['id_peminjaman'=>$peminjaman->id])->all();

                // foreach ($dataLunas as $value) {
                //     $data = Pencicilan::find()->where(['id_status_bayar'=>2])->all();
                //     var_dump($data);
                // }

                // die();

                Yii::$app->session->setFlash('success', "Tambah Data Cicilan Nasabah Berhasil");
                return $this->redirect(['pencicilan/cicilan/','id'=>$model->id_peminjaman]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'peminjaman' => $peminjaman,
            'totalCicilan' => $totalCicilan,
            'info' => $info,
            'rumus' => $rumus,
            'denda' => $denda,
            'cicilanDenda' => $cicilanDenda
        ]);
    }

    /**
     * Updates an existing Pencicilan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        date_default_timezone_set("Asia/Jakarta");

        $model = Pencicilan::find()->where(['id'=>$id])->one();
        $peminjaman = Peminjaman::find()->where(['id'=>$model->id_peminjaman])->one();
        $info = Peminjaman::find()->where(['id'=>$model->id_peminjaman])->one();
        $jenisPeminjaman = PeminjamanJenis::find()->where(['id'=>$info->id_jenis_peminjaman])->one();
        $cicilanDenda = Pencicilan::find()->where(['id'=>$id])->one();
        $data = Pencicilan::find()->select('count(id_peminjaman) as total')->groupBy('id_peminjaman')->where(['id_peminjaman'=>$model->id_peminjaman])->andWhere(['id_status_bayar'=>2])->asArray()->all();
        $cicilan = [];
        foreach ($data as $key => $value) {
            $cicilan = (int)$value['total'];
        }
        $totalCicilan = json_encode($cicilan);

        //lunas dipercepat jaminan
        $rumus = Pencicilan::getLunasDipercepat($peminjaman->id_jenis_peminjaman, $totalCicilan, $peminjaman->durasi, $peminjaman->nominal_peminjaman, $jenisPeminjaman->besar_pinalti_langsung_lunas, $peminjaman->nominal_pencicilan, $peminjaman->id, $peminjaman->nominal_tabungan_ditahan);
        $denda = Peminjaman::getDenda($cicilanDenda->tanggal_jatuh_tempo, $info->nominal_pencicilan, $jenisPeminjaman->besar_denda);
        

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();

             $dataLunas = Pencicilan::find()->where(['id_peminjaman'=>$peminjaman->id])->all();
            // var_dump($dataLunas);die();

            if ($post['cicilan'] == 2) {
                
                //update status lunas
                $peminjaman->id_status_peminjaman = 2;
                $peminjaman->save(false);

                $dataCicilan = Pencicilan::find()->where(['id_peminjaman'=>$peminjaman->id])->all();

                foreach ($dataCicilan as $key => $value) {
                    $value->id_status_bayar = 2;
                    $value->save(false);
                }

                $model->tanggal_waktu_cicilan = date('Y-m-d H:i:s');
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->nominal_denda_dibayar = $denda;
                $model->save(false);

                Yii::$app->session->setFlash('success', "Update Data Cicilan Nasabah Berhasil");
                return $this->redirect(['pencicilan/index']);
            } else {
                $model->id_status_bayar = 2;
                $model->tanggal_waktu_cicilan = date('Y-m-d H:i:s');
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->nominal_denda_dibayar = $denda;
                $model->save(false);

                Yii::$app->session->setFlash('success', "Update Data Cicilan Nasabah Berhasil");
                return $this->redirect(['pencicilan/cicilan/','id'=>$model->id_peminjaman]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'peminjaman' => $peminjaman,
            'totalCicilan' => $totalCicilan,
            'info' => $info,
            'rumus' => $rumus,
            'denda' => $denda,
            'cicilanDenda' => $cicilanDenda
        ]);
    }

    /**
     * Deletes an existing Pencicilan model.
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
     * Finds the Pencicilan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pencicilan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pencicilan::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
