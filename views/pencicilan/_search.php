<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PencicilanSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pencicilan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_peminjaman') ?>

    <?= $form->field($model, 'nominal_cicilan') ?>

    <?= $form->field($model, 'tanggal_waktu_cicilan') ?>

    <?= $form->field($model, 'id_pengguna') ?>

    <?php // echo $form->field($model, 'id_jenis_pencicilan') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
