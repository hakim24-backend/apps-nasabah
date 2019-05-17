<?php

namespace app\controllers;

use Yii;
use app\models\Pencicilan;
use app\models\Peminjaman;
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

    public function actionCicilan($id)
    {
        $searchModel = new PencicilanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['id_peminjaman' => $id]);

        $info = Peminjaman::find()->where(['id'=>$id])->one();
        $data = Pencicilan::find()->select('count(id_peminjaman) as total')->groupBy('id_peminjaman')->where(['id_peminjaman'=>$id])->asArray()->all();
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
        $model = new Pencicilan();
        $peminjaman = Peminjaman::find()->where(['id'=>$id])->one();

        $info = Peminjaman::find()->where(['id'=>$id])->one();
        $data = Pencicilan::find()->select('count(id_peminjaman) as total')->groupBy('id_peminjaman')->where(['id_peminjaman'=>$id])->asArray()->all();
        $cicilan = [];
        foreach ($data as $key => $value) {
            $cicilan = (int)$value['total'];
        }
        $totalCicilan = json_encode($cicilan);

        if ($peminjaman->id_jenis_peminjaman == 1) {
            //lunas dipercepat jaminan
            if ($totalCicilan == '[]') {
                $intervalDurasi = $peminjaman->durasi - 0;
            } else {
                $intervalDurasi = $peminjaman->durasi - $totalCicilan;
            }
            $sisaCicilan = ($peminjaman->nominal_peminjaman/$peminjaman->durasi)*$intervalDurasi;
            $rumus = ($sisaCicilan)+(5/100*$sisaCicilan);
        } else {
            //lunas dipercepat non-jaminan
            if ($totalCicilan == '[]') {
                $intervalDurasi = $peminjaman->durasi - 0;
            } else {
                $intervalDurasi = $peminjaman->durasi - $totalCicilan;
            }
            $sisaCicilan = ($peminjaman->nominal_pencicilan)*$intervalDurasi;
            $rumus = ($sisaCicilan)+(5/100*$sisaCicilan);
        }

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();

            if ($post['cicilan'] == 2) {
                
                //update status lunas
                $peminjaman->id_status_peminjaman = 2;
                $peminjaman->save(false);

                $model->id_peminjaman = $id;
                $model->tanggal_waktu_cicilan = date('Y-m-d H:i:s');
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->save(false);

                Yii::$app->session->setFlash('success', "Tambah Data Cicilan Nasabah Berhasil");
                return $this->redirect(['pencicilan/index']);
            } else {
                $model->id_peminjaman = $id;
                $model->tanggal_waktu_cicilan = date('Y-m-d H:i:s');
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->save(false);

                Yii::$app->session->setFlash('success', "Tambah Data Cicilan Nasabah Berhasil");
                return $this->redirect(['pencicilan/cicilan/','id'=>$id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'peminjaman' => $peminjaman,
            'totalCicilan' => $totalCicilan,
            'info' => $info,
            'rumus' => $rumus
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
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
