<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NasabahRiwayatNomorTeleponSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nasabah-riwayat-nomor-telepon-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_nasabah') ?>

    <?= $form->field($model, 'nomor_telepon') ?>

    <?= $form->field($model, 'tanggal_waktu_pembuatan') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
