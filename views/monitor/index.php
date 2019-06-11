<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use app\models\Peminjaman;
/* @var $this yii\web\View */
/* @var $searchModel app\models\NasabahSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($dataNasabah == 0) {
    $this->title = 'Monitoring Nasabah (Tidak Ada Nasabah Dalam Tanggungan)';
} else {
    $this->title = 'Monitoring Nasabah';
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nasabah-index">

    <div class="box box-info">
        <div class="box-body">

            
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
                    // 'email:email',
                    // [
                    //     'attribute' => 'foto_ktp',
                    //     'format' => 'html',
                    //     'value'=>function($data){
                    //         return Html::img('../../web/foto/'.$data['foto_ktp'],['width' => '150px']);
                    //     }
                    // ],
                    // [
                    //     'attribute' => 'foto_bersama_ktp',
                    //     'format' => 'html',
                    //     'value'=>function($data){
                    //         return Html::img('../../web/foto/'.$data['foto_bersama_ktp'],['width' => '150px']);
                    //     }
                    // ],
                    [
                        'format' => 'raw',
                        'value'=>function($model){
                            return '<span class="label label-danger">Belum Lunas</span>';
                        }
                    ],
                    //'latitude',
                    //'longitude',
                    //'access_token',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{monitor} {view}',
                        'contentOptions' => ['style'=>'text-align: right'],
                        'buttons' => [

                            'monitor' => function($url, $model, $key){
                                $data = Peminjaman::find()->where(['id_nasabah'=>$model['id']])->andWhere(['id_status_peminjaman'=>1])->count();
                                if ($data > 0 ) {
                                    return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-map-marker"></span>']),['monitor/monitor','id'=>$model->id], ['class' => 'btn btn-primary modalButtonView']);
                                }
                            },
                            'view' => function($url, $model, $key){
                                return Html::a(Yii::t('app','{modelClass}',['modelClass'=>'<span class="glyphicon glyphicon-eye-open"></span>']),['monitor/view','id'=>$model->id], ['class' => 'btn btn-warning modalButtonView']);
                            }
                        ],
                    ],
                ],
            ]); ?>

            
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

<?php
    $this->registerJs("

        // $('.modalButtonView').click(function(e) {
        //  e.preventDefault();
        //  $('#modalView').modal('show')
        //  .find('#modalContentView')
        //  .load($(this).attr('href'));
        // });

    ")
?>
