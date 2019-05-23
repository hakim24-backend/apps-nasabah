<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Peminjaman */

$this->title = 'View Data Cicilan Nasabah';
$this->params['breadcrumbs'][] = ['label' => 'Peminjamen', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
function to_rp($val)
{
    return "Rp " . number_format($val,0,',','.');
}
?>
<div class="peminjaman-view">

    <p>
        <a class="btn btn-danger" href="<?php echo Url::to(['pencicilan/index']) ?>">Kembali</a>
    </p>

    <div class="box box-info">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    // 'id',
                    // 'id_nasabah',
                    // 'id_jenis_peminjaman',
                    [
                    'attribute' => 'id_status_peminjaman',
                    'value' => function($model){
                        if ($model->id_status_peminjaman == 1) {
                            return 'Belum Lunas';
                        } else {
                            return 'Lunas';
                        }
                    }
                    ],
                    'nomor_kontrak',
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
                        'attribute' => 'jaminan',
                        'value' => $model->jaminan != null ? $model->jaminan : 'Tidak Ada'
                    ],
                    'nama',
                    'alamat',
                    'nik_ktp',
                    [
                    'attribute' => 'nominal_peminjaman',
                    'value' => function($model){
                        return to_rp($model->nominal_peminjaman);
                    }
                    ],
                    [
                    'attribute' => 'nominal_admin',
                    'value' => function($model){
                        return to_rp($model->nominal_admin);
                    }
                    ],
                    [
                    'attribute' => 'nominal_tabungan_ditahan',
                    'value' => function($model){
                        return to_rp($model->nominal_tabungan_ditahan);
                    }
                    ],
                    [
                    'attribute' => 'nominal_pencicilan',
                    'value' => function($model){
                        return to_rp($model->nominal_pencicilan);
                    }
                    ],
                    'durasi',
                    [
                    'attribute' => 'id_jenis_durasi',
                    'value' => function($model){
                        if ($model->id_jenis_durasi == 1) {
                            return 'Mingguan';
                        } else {
                            return 'Bulanan';
                        }
                    }
                    ],
                    [
                        'attribute' => 'foto_ktp',
                        'format'=>'html',
                        'value'=>function($data){
                            return Html::img('../../web/foto/'.$data['foto_ktp'],['width' => '150px']);
                        }
                    ],
                    [
                        'attribute' => 'foto_bersama_ktp',
                        'format'=>'html',
                        'value'=>function($data){
                            return Html::img('../../web/foto/'.$data['foto_bersama_ktp'],['width' => '150px']);
                        }
                    ],
                    [
                        'attribute' => 'foto_optional',
                        'format'=>'html',
                        'value'=>function($data){
                            return Html::img('../../web/foto/'.$data['foto_optional'],['width' => '150px']);
                        }
                    ],
                    'tanggal_waktu_pembuatan'
                    // 'id_pengguna',
                ],
            ]) ?> 
        </div>
    </div>

</div>
