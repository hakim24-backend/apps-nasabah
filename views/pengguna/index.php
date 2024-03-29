<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PenggunaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Data Pengguna';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pengguna-index">

    <p>
        <?= Html::a('Tambah Pengguna', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="box box-info">
        <div class="box-body">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    'nama',
                    'alamat',
                    'email:email',
                    // 'jenis_kelamin',
                    // 'tempat_lahir',
                    // 'tanggal_lahir',
                    //'id_akun',

                    [
                        'attribute' => 'id_akun',
                        'format' => 'raw',
                        'value'=>function($model){
                            if ($model->akun->id_jenis_akun == 1) {
                                return '<span class="label label-success">Admin</span>';
                            } elseif($model->akun->id_jenis_akun == 3) {
                                return '<span class="label label-warning">Owner</span>';
                            } elseif ($model->akun->id_jenis_akun == 4) {
                                return '<span class="label label-danger">Developer</span>';
                            }
                        }
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['pengguna/view','id'=>$model->id], ['class' => 'btn btn-warning modalButtonView']);
                            },
                            'update'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-pencil"></span>']), ['pengguna/update','id'=>$model->id], ['class' => 'btn btn-info modalButtonUpdate']);
                            },
                            'delete'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-trash"></span>']), ['pengguna/delete','id'=>$model->id], ['class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Apakah anda yakin untuk menghapus data ini ?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
