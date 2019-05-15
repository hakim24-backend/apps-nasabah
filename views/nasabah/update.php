<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Nasabah */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Update Nasabah';
$this->params['breadcrumbs'][] = ['label' => 'Nasabahs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="nasabah-form">

    <div class="box box-info">
        <div class="box-body">
            <?php $form = ActiveForm::begin([
		    	'options' => ['enctype'=>'multipart/form-data']
		    ]); ?>

            <?= $form->field($model, 'nama')->textInput(['maxlength' => true, 'required'=>true]) ?>

            <?= $form->field($model, 'alamat')->textInput(['maxlength' => true, 'required'=>true]) ?>

            <?= $form->field($model, 'tempat_lahir')->textInput(['maxlength' => true, 'required'=>true]) ?>

            <?= $form->field($model, 'tanggal_lahir')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Masukkan Tanggal Lahir ...','required'=>true],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose'=>true
                ]
            ])?>

            <!-- <?= $form->field($model, 'tanggal_lahir')->textInput() ?> -->

            <!-- <?= $form->field($model, 'jenis_kelamin')->textInput(['maxlength' => true, 'required'=>true]) ?> -->

            <?= $form->field($model, 'nomor_telepon')->textInput(['maxlength' => true, 'required'=>true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'required'=>true]) ?>

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

            <!-- <?= $form->field($model, 'foto_ktp')->textInput(['maxlength' => true, 'required'=>true]) ?> -->

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

            <!-- <?= $form->field($model, 'foto_bersama_ktp')->textInput(['maxlength' => true, 'required'=>true]) ?> -->

            <?= $form->field($model, 'jenis_kelamin')->radioList(array('Pria'=>'Pria','Wanita'=>'Wanita')); ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <a class="btn btn-danger" href="<?= Url::to(Yii::$app->request->referrer);?>">Batal</a>
            </div>

            <?php ActiveForm::end(); ?>  
        </div>
    </div>

</div>
