<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\file\FileInput;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Peminjaman */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="peminjaman-form">

    <div class="box box-info">
        <div class="box-body">
            <?php $form = ActiveForm::begin([
		    	'options' => ['enctype'=>'multipart/form-data']
		    ]); ?>

            <!-- <?= $form->field($model, 'nama')->textInput(['maxlength' => true]) ?> -->

            <label>Status Peminjaman</label>
            <?= Html::dropDownlist('peminjaman',$model->id_status_peminjaman,[1=>'Belum Lunas',2=>'Lunas'], ['required' => true, 'class' => 'form-control', 'id' => 'peminjaman', 'style' => 'width: 100%']) ?>
            <br>

            <?php echo $form->field($model, 'id_nasabah')->widget(Select2::classname(), [
                'data' => $nama,
                'options' => [
                    'placeholder' => 'Pilih Nama...', 
                    'id'=>'nama',
                    'required'=>true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'initialize' => true,
                ],
            ]) ?>

            <?= $form->field($model, 'alamat')->textInput(['maxlength' => true,'required'=>true]) ?>

            <?= $form->field($model, 'nik_ktp')->textInput(['maxlength' => true,'required'=>true]) ?>

            <?= $form->field($model, 'nominal_peminjaman')->widget(MaskMoney::classname(), 
              [
                'options' => [
                    'required'=>true,
                    'disabled' => true
                ],
                'pluginOptions' => [
                    'prefix' => 'Rp. ',
                    'suffix' => '',
                    'affixesStay' => true,
                    'thousands' => '.',
                    'decimal' => ',',
                    'precision' => 0, 
                    'allowZero' => false,
                    'allowNegative' => false,
                ]
            ]) ?>

            <!-- <?= $form->field($model, 'nominal_peminjaman')->textInput() ?> -->

            <label>Jenis Peminjaman</label>
            <?= Html::dropDownlist('status',$model->id_jenis_peminjaman,[1=>'Jaminan',2=>'Non-Jaminan'], ['prompt' => 'Pilih Status Peminjaman...', 'required' => true,'disabled'=>true, 'class' => 'form-control', 'id' => 'status', 'style' => 'width: 100%']) ?>
            <br>

            <div id="jenis">
            </div>

            <div id="durasi">
            </div>

            <div id="jaminan">
                <?= $form->field($model, 'jaminan')->textInput(['maxlength' => true,'required'=>true,'disabled'=>true]) ?>
            </div>

            <label>Jenis Durasi</label>
            <?= Html::dropDownlist('jenis-durasi',$model->id_jenis_durasi,[1=>'Mingguan',2=>'Bulanan'], ['prompt' => 'Pilih Jenis Durasi...', 'required' => true,'disabled'=>true, 'class' => 'form-control', 'id' => 'jenis-durasi', 'style' => 'width: 100%']) ?>
            <br>

            <?= $form->field($model, 'durasi')->textInput(['maxlength' => true,'required' => true,'disabled'=>true]) ?>

            <?php
		        echo FileInput::widget([
		            'model' => $model,
		            'attribute' => 'foto_ktp',
		            'options'=>[
		                'accept' => 'image/*'
		            ],
		            'pluginOptions' => [
		                'initialPreview'=>[
		                    Html::img(Yii::$app->urlManager->createUrl(['foto/'.$model->foto_ktp]),['style' => 'width:150px;']),
		                ],
		                'removeClass' => 'btn btn-danger',
		                'showUpload' => false,
		                'removeIcon' => '<i class="glyphicon glyphicon-trash"></i>',
		                'overwriteInitial'=>false,
		            ]
		        ]);
		    ?><br>

            <!-- <?= $form->field($model, 'foto_ktp')->textInput(['maxlength' => true]) ?> -->

            <?php
		        echo FileInput::widget([
		            'model' => $model,
		            'attribute' => 'foto_bersama_ktp',
		            'options'=>[
		                'accept' => 'image/*'
		            ],
		            'pluginOptions' => [
		                'initialPreview'=>[
		                    Html::img(Yii::$app->urlManager->createUrl(['foto/'.$model->foto_bersama_ktp]),['style' => 'width:150px;']),
		                ],
		                'removeClass' => 'btn btn-danger',
		                'showUpload' => false,
		                'removeIcon' => '<i class="glyphicon glyphicon-trash"></i>',
		                'overwriteInitial'=>false,
		            ]
		        ]);
		    ?><br>

            <?php
                echo FileInput::widget([
                    'model' => $model,
                    'attribute' => 'foto_optional',
                    'options'=>[
                        'accept' => 'image/*'
                    ],
                    'pluginOptions' => [
                        'initialPreview'=>[
                            Html::img(Yii::$app->urlManager->createUrl(['foto/'.$model->foto_optional]),['style' => 'width:150px;']),
                        ],
                        'removeClass' => 'btn btn-danger',
                        'showUpload' => false,
                        'removeIcon' => '<i class="glyphicon glyphicon-trash"></i>',
                        'overwriteInitial'=>false,
                    ]
                ]);
            ?><br>

            <!-- <?= $form->field($model, 'foto_bersama_ktp')->textInput(['maxlength' => true]) ?> -->

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <a class="btn btn-danger" href="<?php echo Url::to(['peminjaman/index']) ?>">Kembali</a>
            </div>

            <?php ActiveForm::end(); ?>  
        </div>
    </div>

</div>

<?php
    $this->registerJs("

        $('#status').on('change',function(){
        	var model_id = '".$model->id."';
            var id = $('#status').val();

            if (id == 1){
            	$.ajax({
	              url : '" . Yii::$app->urlManager->baseUrl."/peminjaman/get-status-update?id='+id+'&model_id='+model_id,
	              dataType : 'html',
	              success: function (data) {
	                $('#jenis').html(data);
	              }
	            })
            } else {
                $('#jaminan').hide();
            	$('#jaminan-update').hide();
            }
            
        });

    ")
?>
