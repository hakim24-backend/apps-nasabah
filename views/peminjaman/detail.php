<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\PeminjamanJenis;

/* @var $this yii\web\View */
/* @var $model app\models\Pencicilan */

$this->title = 'Rincian Peminjaman';
$this->params['breadcrumbs'][] = ['label' => 'Rincian Peminjaman', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
function to_rp($val)
{
    return "Rp " . number_format($val,0,',','.');
}
$jenisPeminjaman = PeminjamanJenis::find()->where(['id'=>$model->id_jenis_peminjaman])->one();
?>

<p>
    <a class="btn btn-success" href="<?php echo Url::to(['peminjaman/index']) ?>">Kembali Ke Menu Utama</a>
</p>

<div class="box box-info">
	<div class="box-body">
		<h3>Selamat!!!</h3>
		<h3>Data peminjaman berhasil dibuat</h3>
		<h3>Untuk rincian peminjaman : </h3>
		<h3>Jenis Peminjaman = <?= $jenisPeminjaman->jenis_peminjaman ?></h3>
		<h3>Nominal Peminjaman = <?= to_rp($model->nominal_peminjaman) ?></h3>
		<h3>Biaya untuk admin = <?= to_rp($model->nominal_admin) ?></h3>
		<h3>Tabungan ditahan = <?= to_rp($model->nominal_tabungan_ditahan) ?></h3>
		<h3>Cicilan Per Bulan = <?= to_rp($model->nominal_pencicilan) ?></h3>
		<h3>Uang yang Diterima nasabah = <?= to_rp($model->nominal_peminjaman  -  $model->nominal_admin) ?></h3>
	</div>
</div>