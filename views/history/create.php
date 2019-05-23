<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NasabahRiwayatNomorTelepon */

$this->title = 'Create Nasabah Riwayat Nomor Telepon';
$this->params['breadcrumbs'][] = ['label' => 'Nasabah Riwayat Nomor Telepons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nasabah-riwayat-nomor-telepon-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
