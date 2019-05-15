<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Peminjaman */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Peminjamen', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="peminjaman-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_nasabah',
            'id_jenis_peminjaman',
            'nomor_kontrak',
            'nama',
            'alamat',
            'nik_ktp',
            'nominal_peminjaman',
            'id_jenis_durasi',
            'durasi',
            'jaminan',
            'foto_ktp',
            'foto_bersama_ktp',
            'tanggal_waktu_pembuatan',
            'id_status_peminjaman',
            'id_pengguna',
        ],
    ]) ?>

</div>
