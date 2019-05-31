<?php
date_default_timezone_set("Asia/Jakarta");
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Pencicilan;
use kartik\date\DatePicker;
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
        <a class="btn btn-danger" href="<?php echo Url::to(['pencicilan/index']) ?>">Kembali</a>
        <?= Html::a('Reset Filter', ['pencicilan/cicilan','id'=>$id], ['class' => 'btn btn-success']) ?>
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
            <?php if ($info->id_jenis_durasi == 1) { ?>
                Cicilan per minggu = <?=to_rp($info->nominal_pencicilan)?><br>
            <?php } else { ?>
                Cicilan per bulan = <?=to_rp($info->nominal_pencicilan)?><br>
            <?php } ?>
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
                    [
                    'attribute' => 'nominal_denda_berhenti',
                    'value' => function($model){
                        return to_rp($model->nominal_denda_berhenti);
                    }
                    ],
                    [
                    'attribute' => 'tanggal_waktu_cicilan',
                    'filter' => DatePicker::widget([
                                        'model' => $searchModel, 
                                        'attribute' => 'tanggal_waktu_cicilan',
                                        'options' => ['placeholder' => 'Pilih Tanggal ...'],
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'yyyy-mm-dd'
                                        ]
                                    ]),
                    'value' => function($model){
                        if ($model->tanggal_waktu_cicilan == null) {
                            return 'Belum ada';
                        } else {
                            $date=date_create($model->tanggal_waktu_cicilan);
                            return date_format($date, 'd F Y');
                        }
                    }
                    ],
                    [
                    'attribute' => 'id_status_bayar',
                    'filter' => Html::dropDownlist('PencicilanSearch[id_status_bayar]',null,[1=>'Belum Lunas', 2=>'Lunas'], ['prompt' => 'Pilih Status', 'required' => true, 'class' => 'form-control', 'id' => 'status', 'style' => 'width: 100%']),
                    'format' => 'raw',
                    'value' => function($model){
                        if ($model->id_status_bayar == 1) {
                            return '<span class="label label-info">Belum Lunas</span>';
                        } else {
                            return '<span class="label label-success">Lunas</span>';
                        }
                    }
                    ],
                    // 'id_pengguna',
                    //'id_jenis_pencicilan',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{create} {view}',
                        'contentOptions' => ['style'=>'text-align: right'],
                        'buttons' => [
                            'create' => function($url, $model, $key){
                                if ($model->id_status_bayar == 1) {
                                    return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-plus"></span>']),['pencicilan/create','id'=>$model->id], ['class' => 'btn btn-success modalButtonView']);
                                }
                            },
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['pencicilan/view','id'=>$model->id], ['class' => 'btn btn-warning modalButtonView']);
                            }
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>