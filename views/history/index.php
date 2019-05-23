<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NasabahRiwayatNomorTeleponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nasabah Riwayat Nomor Telepons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nasabah-riwayat-nomor-telepon-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Nasabah Riwayat Nomor Telepon', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_nasabah',
            'nomor_telepon',
            'tanggal_waktu_pembuatan',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
