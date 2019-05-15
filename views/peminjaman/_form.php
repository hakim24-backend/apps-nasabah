<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Peminjaman */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="peminjaman-form">

    <div class="box box-info">
        <div class="box-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'nomor_kontrak')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'nama')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'alamat')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'nik_ktp')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'nominal_peminjaman')->textInput() ?>

            <?= $form->field($model, 'id_jenis_durasi')->textInput() ?>

            <?= $form->field($model, 'durasi')->textInput() ?>

            <?= $form->field($model, 'jaminan')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'foto_ktp')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'foto_bersama_ktp')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'tanggal_waktu_pembuatan')->textInput() ?>

            <?= $form->field($model, 'id_status_peminjaman')->textInput() ?>

            <?= $form->field($model, 'id_pengguna')->textInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>  
        </div>
    </div>

</div>
