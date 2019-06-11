<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\PeminjamanSearch;
use kartik\select2\Select2;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PeminjamanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Peminjaman';
$this->params['breadcrumbs'][] = $this->title;
function to_rp($val)
{
    return "Rp " . number_format($val,0,',','.');
}
?>
<div class="peminjaman-index">

    <p>
        <?= Html::a('Tambah Data Peminjaman', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset Filter', ['index'], ['class' => 'btn btn-success']) ?>
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
                    // 'id_nasabah',
                    // 'id_jenis_peminjaman',
                    // 'nomor_kontrak',
                    [
                        'attribute' => 'tanggal_waktu_pembuatan',
                        'filter' => DatePicker::widget([
                                        'model' => $searchModel, 
                                        'attribute' => 'tanggal_waktu_pembuatan',
                                        'options' => ['placeholder' => 'Pilih Tanggal ...'],
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'yyyy-mm-dd'
                                        ]
                                    ]),
                        'value'=>function($model){
                            $date=date_create($model->tanggal_waktu_pembuatan);
                            return date_format($date, 'd F y');
                        }
                    ],
                    // 'nik_ktp',
                    'nama',
                    // 'alamat',
                    [
                    'attribute' => 'nominal_peminjaman',
                    'value' => function($model){
                        return to_rp($model->nominal_peminjaman);
                    }
                    ],
                    //'id_jenis_durasi',
                    //'durasi',
                    //'jaminan',
                    [
                    'attribute' => 'id_status_peminjaman',
                    'filter' => Html::dropDownlist('PeminjamanSearch[id_status_peminjaman]',null,[1=>'Belum Lunas', 2=>'Lunas'], ['prompt' => 'Pilih Status', 'required' => true, 'class' => 'form-control', 'id' => 'status', 'style' => 'width: 100%']),
                    'format' => 'raw',
                    'value' => function($model){
                        if ($model->id_status_peminjaman == 1) {
                            return '<span class="label label-info">Belum Lunas</span>';
                        } else {
                            return '<span class="label label-success">Lunas</span>';
                        }
                    }
                    ],
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
                    //'id_status_peminjaman',
                    //'id_pengguna',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update}',
                        'buttons' => [
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['peminjaman/view','id'=>$model->id], ['class' => 'btn btn-warning modalButtonView']);
                            },
                            'update'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-pencil"></span>']), ['peminjaman/update','id'=>$model->id], ['class' => 'btn btn-info modalButtonUpdate']);
                            }
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
