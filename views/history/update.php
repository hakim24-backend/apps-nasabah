<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NasabahRiwayatNomorTelepon */

$this->title = 'Update Nasabah Riwayat Nomor Telepon: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nasabah Riwayat Nomor Telepons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nasabah-riwayat-nomor-telepon-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
