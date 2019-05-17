<?php

use yii\helpers\Html;
use yii\grid\GridView;

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
                    // 'tanggal_waktu_pembuatan',
                    [
                        'attribute' => 'foto_ktp',
                        'format' => 'html',
                        'value'=>function($data){
                            return Html::img('../../web/foto/'.$data['foto_ktp'],['width' => '150px']);
                        }
                    ],
                    [
                        'attribute' => 'foto_bersama_ktp',
                        'format' => 'html',
                        'value'=>function($data){
                            return Html::img('../../web/foto/'.$data['foto_bersama_ktp'],['width' => '150px']);
                        }
                    ],
                    //'id_status_peminjaman',
                    //'id_pengguna',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['peminjaman/view','id'=>$model->id], ['class' => 'btn btn-warning modalButtonView']);
                            },
                            'update'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-pencil"></span>']), ['peminjaman/update','id'=>$model->id], ['class' => 'btn btn-info modalButtonUpdate']);
                            },
                            'delete'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-trash"></span>']), ['peminjaman/delete','id'=>$model->id], ['class' => 'btn btn-danger',
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
