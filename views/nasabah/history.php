<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\NasabahBukuTeleponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Riwayat Nomor Telepon Nasabah';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nasabah-buku-telepon-index">

    <p>
        <a class="btn btn-danger" href="<?php echo Url::to(['nasabah/index']) ?>">Kembali</a>
    </p>

    <div class="box box-info">
        <div class="box-body">
            
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'filterModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'nomor_telepon',
                    'tanggal_waktu_pembuatan',

                ],
            ]); ?>

            
        </div>
    </div>

</div>
