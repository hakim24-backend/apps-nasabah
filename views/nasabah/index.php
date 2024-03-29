<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use app\models\Peminjaman;
/* @var $this yii\web\View */
/* @var $searchModel app\models\NasabahSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Nasabah';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nasabah-index">

    <p>
        <?= Html::a('Tambah Nasabah', ['create'], ['class' => 'btn btn-primary']) ?>
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
                    // 'id_akun',
                    'nama',
                    'alamat',
                    // 'tempat_lahir',
                    // 'tanggal_lahir',
                    // 'jenis_kelamin',
                    [
                        'attribute' => 'id_akun',
                        'format' => 'raw',
                        'value'=>function($model){
                            if ($model->akun->id_status_akun == 1) {
                                return '<span class="label label-success">Aktif</span>';
                            } elseif($model->akun->id_status_akun == 2) {
                                return '<span class="label label-warning">Tidak Aktif</span>';
                            } elseif ($model->akun->id_status_akun == 3) {
                                return '<span class="label label-danger">Belum terverifikasi</span>';
                            }
                        }
                    ],
                    // 'email:email',
                    // [
                    //     'attribute' => 'foto_ktp',
                    //     'format' => 'html',
                    //     'value'=>function($data){
                    //         return Html::img('../../web/foto/'.$data['foto_ktp'],['width' => '150px']);
                    //     }
                    // ],
                    // [
                    //     'attribute' => 'foto_bersama_ktp',
                    //     'format' => 'html',
                    //     'value'=>function($data){
                    //         return Html::img('../../web/foto/'.$data['foto_bersama_ktp'],['width' => '150px']);
                    //     }
                    // ],
                    //'latitude',
                    //'longitude',
                    //'access_token',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{history} {telepon} {view} {update} {delete}',
                        'contentOptions' => ['style'=>'text-align: right'],
                        'buttons' => [
                            
                            'history' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-refresh"></span>']),['nasabah/history','id'=>$model->id], ['class' => 'btn btn-primary modalButtonView']);
                            },
                            'telepon' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-book"></span>']),['nasabah/phone','id'=>$model->id], ['class' => 'btn btn-success modalButtonView']);
                            },
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['nasabah/view','id'=>$model->id], ['class' => 'btn btn-warning modalButtonView']);
                            },
                            'update'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-pencil"></span>']), ['nasabah/update','id'=>$model->id], ['class' => 'btn btn-info modalButtonUpdate']);
                            },
                            'delete'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-trash"></span>']), ['nasabah/delete','id'=>$model->id], ['class' => 'btn btn-danger',
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

<?php
    Modal::begin([
        'header' => 'View Data Nasabah',
        'id' => 'modalView',
        'size' => 'modal-md',
    ]);
    echo "<div id='modalContentView'></div>";
    Modal::end();
?>

<?php
    $this->registerJs("

        // $('.modalButtonView').click(function(e) {
        //  e.preventDefault();
        //  $('#modalView').modal('show')
        //  .find('#modalContentView')
        //  .load($(this).attr('href'));
        // });

    ")
?>
