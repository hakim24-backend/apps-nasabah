<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PeminjamanSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="peminjaman-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_nasabah') ?>

    <?= $form->field($model, 'id_jenis_peminjaman') ?>

    <?= $form->field($model, 'nomor_kontrak') ?>

    <?= $form->field($model, 'nama') ?>

    <?php // echo $form->field($model, 'alamat') ?>

    <?php // echo $form->field($model, 'nik_ktp') ?>

    <?php // echo $form->field($model, 'nominal_peminjaman') ?>

    <?php // echo $form->field($model, 'id_jenis_durasi') ?>

    <?php // echo $form->field($model, 'durasi') ?>

    <?php // echo $form->field($model, 'jaminan') ?>

    <?php // echo $form->field($model, 'foto_ktp') ?>

    <?php // echo $form->field($model, 'foto_bersama_ktp') ?>

    <?php // echo $form->field($model, 'tanggal_waktu_pembuatan') ?>

    <?php // echo $form->field($model, 'id_status_peminjaman') ?>

    <?php // echo $form->field($model, 'id_pengguna') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
