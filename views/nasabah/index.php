<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\NasabahSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Nasabah';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nasabah-index">

    <p>
        <?= Html::a('Tambah Nasabah', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box box-info">
        <div class="box-body">

            <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    // 'id_akun',
                    'nama',
                    'alamat',
                    // 'tempat_lahir',
                    // 'tanggal_lahir',
                    // 'jenis_kelamin',
                    'nomor_telepon',
                    // 'email:email',
                    'foto_ktp',
                    'foto_bersama_ktp',
                    //'latitude',
                    //'longitude',
                    //'access_token',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>

            <?php Pjax::end(); ?>  
        </div>
    </div>

</div>
