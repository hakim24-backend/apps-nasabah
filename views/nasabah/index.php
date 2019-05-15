<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
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
                    [
                        'attribute' => 'foto_ktp',
                        'format' => 'html',
                        'value'=>function($data){
                            return Html::img('../../web/foto/'.$data['foto_ktp'],['width' => '150px']);
                        }
                    ],
                    [
                        'attribute' => 'foto_bersama_ktp',
                        'format' => 'html',
                        'value'=>function($data){
                            return Html::img('../../web/foto/'.$data['foto_bersama_ktp'],['width' => '150px']);
                        }
                    ],
                    //'latitude',
                    //'longitude',
                    //'access_token',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['nasabah/view','id'=>$model->id], ['class' => 'btn btn-danger modalButtonView']);
                            },
                            'update'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-pencil"></span>']), ['nasabah/update','id'=>$model->id], ['class' => 'btn btn-info modalButtonUpdate']);
                            },
                            'delete'=> function($url, $model, $key){
                                return  Html::a(Yii::t('app', ' {modelClass}', ['modelClass' => '<span class="glyphicon glyphicon-trash"></span>']), ['nasabah/delete','id'=>$model->id], ['class' => 'btn btn-warning',
                                    'data' => [
                                        'confirm' => 'Apakah anda yakin untuk menghapus data ini ?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>  
        </div>
    </div>

</div>

<?php
    Modal::begin([
        'header' => 'View Data Nasabah',
        'id' => 'modalView',
        'size' => 'modal-md',
    ]);
    echo "<div id='modalContentView'></div>";
    Modal::end();
?>
