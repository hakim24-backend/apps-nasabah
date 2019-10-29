<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\PeminjamanSearch;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PencicilanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Data Cicilan Nasabah';
$this->params['breadcrumbs'][] = $this->title;
function to_rp($val)
{
    return "Rp " . number_format($val,0,',','.');
}
?>
<div class="pencicilan-index">

    <p>
        <!-- <?= Html::a('Tambah Cicilan Nasabah', ['create'], ['class' => 'btn btn-primary']) ?> -->
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
                    'nomor_kontrak',
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
                    [
                    'attribute' => 'id_jenis_peminjaman',
                    'filter' => Html::dropDownlist('PeminjamanSearch[id_jenis_peminjaman]',null,[1=>'Jaminan', 2=>'Non-Jaminan'], ['prompt' => 'Pilih Jenis', 'required' => true, 'class' => 'form-control', 'id' => 'status', 'style' => 'width: 100%']),
                    'value' => function($model){
                        if ($model->id_jenis_peminjaman == 1) {
                            return 'Jaminan';
                        } else {
                            return 'Non-Jaminan';
                        }
                    }
                    ],
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
                    //'id_jenis_durasi',
                    //'durasi',
                    //'jaminan',
                    // 'tanggal_waktu_pembuatan',
                    //'id_status_peminjaman',
                    //'id_pengguna',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{cicilan} {view}',
                        'contentOptions' => ['style'=>'text-align: right'],
                        'buttons' => [
                            'cicilan' => function($url, $model, $key){
                                if ($model->id_status_peminjaman == 1) {
                                    return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-plus"></span>']),['pencicilan/cicilan','id'=>$model->id], ['class' => 'btn btn-success modalButtonView']);
                                }
                            },
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['pencicilan/view-cicilan','id'=>$model->id], ['class' => 'btn btn-warning modalButtonView']);
                            },
                        ],
                    ],
                ],
            ]); ?>

        </div>
    </div>

</div>
