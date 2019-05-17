<?php
date_default_timezone_set("Asia/Jakarta");
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Pencicilan;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PencicilanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Data Cicilan';
$this->params['breadcrumbs'][] = $this->title;
function to_rp($val)
{
    return "Rp " . number_format($val,0,',','.');
}
?>
<div class="pencicilan-index">

    <p>
        <?= Html::a('Tambah Data Cicilan', ['create','id' => $id], ['class' => 'btn btn-primary']) ?>
        <a class="btn btn-danger" href="<?php echo Url::to(['pencicilan/index']) ?>">Kembali</a>
    </p>

    <div class="box box-info">
        <div class="box-body">
            <b>Info</b><br>
            <?php if ($info->id_jenis_peminjaman == 1) { ?>
                Jenis Peminjaman = Jaminan<br>
            <?php } else { ?>
                Jenis Peminjaman = Non-jaminan<br>
            <?php } ?>
            Nominal Peminjaman = <?=to_rp($info->nominal_peminjaman)?><br>
            Cicilan per bulan = <?=to_rp($info->nominal_pencicilan)?><br>
            Tanggal Peminjaman = <?=date("d/m/Y", strtotime($info->tanggal_waktu_pembuatan))?><br>
            <!-- Jatuh Tempo = <?=date("d/m/Y", strtotime($info->tanggal_waktu_pembuatan."+1 months"))?><br> -->
            <?php if ($totalCicilan == '[]' ) { ?>
                <?php if ($info->id_jenis_durasi == 1) { ?>
                    Durasi = <?=$info->durasi?> Minggu<br>
                    Total Cicilan Perminggu = 0x
                <?php } else { ?>
                    Durasi = <?=$info->durasi?> Bulan<br>
                    Total Cicilan Perbulan = 0x
                <?php } ?>
            <?php } else { ?>
                <?php if ($info->id_jenis_durasi == 1) { ?>
                    Durasi = <?=$info->durasi?> Minggu<br>
                    Total Cicilan Perminggu = <?=$totalCicilan?>x
                <?php } else { ?>
                    Durasi = <?=$info->durasi?> Bulan<br>
                    Total Cicilan Perbulan = <?=$totalCicilan?>x
                <?php } ?>
            <?php } ?> 
        </div>
    </div>

    <div class="box box-info">
        <div class="box-body">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    // 'id_peminjaman',
                    [
                    'attribute' => 'nominal_cicilan',
                    'value' => function($model){
                        return to_rp($model->nominal_cicilan);
                    }
                    ],
                    'tanggal_waktu_cicilan',
                    // 'id_pengguna',
                    //'id_jenis_pencicilan',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {delete}',
                        'buttons' => [
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['pencicilan/view','id'=>$model->id], ['class' => 'btn btn-danger modalButtonView']);
                            },
                            'delete'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-trash"></span>']), ['pencicilan/delete','id'=>$model->id], ['class' => 'btn btn-warning',
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