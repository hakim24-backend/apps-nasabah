<?php

namespace app\controllers;

use Yii;
use app\models\Nasabah;
use app\models\Akun;
use app\models\Peminjaman;
use app\models\NasabahSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MonitorController implements the CRUD actions for Nasabah model.
 */
class MonitorController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'view', 'monitor'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
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
        $dataProvider->query->joinWith(['peminjamen'])->andWhere(['id_status_peminjaman'=>1]);
        $dataNasabah = Nasabah::find()->joinWith(['peminjamen'])->andWhere(['id_status_peminjaman'=>1])->count();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataNasabah' => $dataNasabah
        ]);
    }

    public function actionView($id)
    {
        $model = Nasabah::find()->where(['id'=>$id])->one();
        $akun = Akun::find()->where(['id'=>$model->id_akun])->one();

        return $this->render('view', [
            'model' => $model,
            'akun' => $akun
        ]);
    }

    public function actionMonitor($id)
    {
        $model = Nasabah::find()->where(['id'=>$id])->one();

        return $this->render('monitor', [
            'model' => $model,
        ]);
    }
}
