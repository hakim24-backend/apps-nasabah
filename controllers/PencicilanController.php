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

        $totalCicilan = Pencicilan::getTotalCicilan($model->id_peminjaman);

        //lunas dipercepat jaminan
        $rumus = Pencicilan::getLunasDipercepat($peminjaman->id_jenis_peminjaman, $totalCicilan, $peminjaman->durasi, $peminjaman->nominal_peminjaman, $jenisPeminjaman->besar_pinalti_langsung_lunas, $peminjaman->nominal_pencicilan, $peminjaman->id, $peminjaman->nominal_tabungan_ditahan);
        $denda = Peminjaman::getDenda($model->tanggal_jatuh_tempo, $info->nominal_pencicilan, $jenisPeminjaman->besar_denda);

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();

            if ($post['cicilan'] == 2) {
                $nominal_lunas = str_ireplace('.', '', $post['nominal_lunas']);
                $nominal_lunas = str_ireplace('Rp ', '', $nominal_lunas);

                //update status lunas
                $peminjaman->id_status_peminjaman = 2;
                $peminjaman->save(false);

                $dataCicilan = Pencicilan::find()->where(['id_peminjaman'=>$peminjaman->id])->all();

                foreach ($dataCicilan as $key => $value) {
                    $value->id_status_bayar = 2;
                    $value->save(false);
                }

                $model->nominal_cicilan = $nominal_lunas;
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->nominal_denda_dibayar = $denda;
                $model->save(false);

                Yii::$app->session->setFlash('success', "Tambah Data Cicilan Nasabah Berhasil");
                return $this->redirect(['pencicilan/index']);
            } else {
                $nominal_sesuai_durasi = str_ireplace('.', '', $post['nominal_sesuai_durasi']);
                $nominal_sesuai_durasi = str_ireplace('Rp ', '', $nominal_sesuai_durasi);

                if($model->nominal_cicilan == null){
                    //saat belum ada pembayaran pada suatu pencicilan
                    if($nominal_sesuai_durasi == $peminjaman->nominal_pencicilan){
                        //bayar cicilan pokok saja
                        $model->nominal_cicilan = $peminjaman->nominal_pencicilan
                        if($denda == 0){
                            $model->id_status_bayar = 2;
                        }
                    } elseif ($nominal_sesuai_durasi < $peminjaman->nominal_pencicilan) {
                        //bayar uang kurang
                        $model->nominal_cicilan = $nominal_sesuai_durasi;
                    } else {
                        $model->nominal_cicilan = $peminjaman->nominal_pencicilan;
                        $sisa = $nominal_sesuai_durasi - $model->nominal_cicilan

                        if($denda == 0){
                            $next_pencicilan = Pencicilan::find()->where(['periode'=>(($model->periode)+1)])->one();
                            if($next_pencicilan){
                                // nextpencicilan belum ada bayar sama sekali
                            }
                        } else {
                            if($denda < $sisa){
                                $model->nominal_denda_dibayar = $denda;
                            } else {
                                $model->nominal_denda_dibayar = $sisa;
                                $model->nominal_denda_berhenti = $denda - $model->nominal_denda_dibayar;
                            }
                        }

                        do{
                            

                        } while ($nominal_sesuai_durasi > 0);
                        
                    }
                } 
                
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->nominal_denda_dibayar = $denda;
                $model->save(false);

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

                $nominal_lunas = str_ireplace('.', '', $post['nominal_lunas']);
                $nominal_lunas = str_ireplace('Rp ', '', $nominal_lunas);
                $model->nominal_cicilan = $nominal_lunas;
                $model->id_jenis_pencicilan = $post['cicilan'];
                $model->nominal_denda_dibayar = $denda;
                $model->save(false);

                Yii::$app->session->setFlash('success', "Update Data Cicilan Nasabah Berhasil");
                return $this->redirect(['pencicilan/index']);
            } else {
                $model->id_status_bayar = 2;
                if($post ==  null) {
                    $nominal_sesuai_durasi = str_ireplace('.', '', $post['nominal_sesuai_durasi']);
                    $nominal_sesuai_durasi = str_ireplace('Rp ', '', $nominal_sesuai_durasi);
                    $model->nominal_cicilan = $nominal_sesuai_durasi;
                }
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

    public function actionGetNominalCicilan($id,$cicilan_denda,$cicilan_lunas_denda)
    {
        
        function to_rp($val)
        {
            return "Rp " . number_format($val,0,',','.');
        }
        if ($id == 1) {
            echo '
                <label>Nominal Cicilan</label>
                <input type="text" readonly id="nominal_sesuai_durasi" class="form-control" name="nominal_sesuai_durasi" value="'.to_rp($cicilan_denda).'"><br>
            ';
        } elseif ($id == 2) {
            echo '
                <label>Nominal Cicilan</label>
                <input type="text" readonly id="nominal_lunas" class="form-control" name="nominal_lunas" value="'.to_rp($cicilan_lunas_denda).'"><br>
            ';
        } 
        // elseif ($id == 3) {
        //     echo '
        //         <label>Nominal Cicilan</label>
        //         <input type="text" id="nominal_lainnya" class="form-control" name="nominal_lainnya"><br>
        //     ';
        // } 
        else {
            echo '';
        }
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
