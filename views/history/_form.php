<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NasabahRiwayatNomorTelepon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nasabah-riwayat-nomor-telepon-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_nasabah')->textInput() ?>

    <?= $form->field($model, 'nomor_telepon')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tanggal_waktu_pembuatan')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
