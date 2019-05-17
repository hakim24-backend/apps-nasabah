<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pencicilan */

$this->title = 'Tambah Data Cicilan Nasabah';
$this->params['breadcrumbs'][] = ['label' => 'Pencicilans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pencicilan-create">

    <?= $this->render('_form', [
        'model' => $model,
        'peminjaman' => $peminjaman,
        'totalCicilan' => $totalCicilan,
        'info' => $info,
        'rumus' => $rumus
    ]) ?>

</div>
