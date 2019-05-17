<?php

use yii\helpers\Html;
use yii\grid\GridView;

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

    <!-- <p>
        <?= Html::a('Tambah Cicilan Nasabah', ['create'], ['class' => 'btn btn-primary']) ?>
    </p> -->

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
                        'buttons' => [
                            'cicilan' => function($url, $model, $key){
                                if ($model->id_status_peminjaman == 1) {
                                    return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-plus"></span>']),['pencicilan/cicilan','id'=>$model->id], ['class' => 'btn btn-success modalButtonView']);
                                }
                            },
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['peminjaman/view','id'=>$model->id], ['class' => 'btn btn-danger modalButtonView']);
                            },
                        ],
                    ],
                ],
            ]); ?>

        </div>
    </div>

</div>
