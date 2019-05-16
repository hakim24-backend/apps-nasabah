<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Pengguna */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pengguna-form">

    <div class="box box-info">
        <div class="box-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'nama')->textInput(['maxlength' => true, 'required'=>true]) ?>

            <?= $form->field($model, 'alamat')->textInput(['maxlength' => true, 'required'=>true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'required'=>true]) ?>

            <?= $form->field($model, 'jenis_kelamin')->radioList(array('Pria'=>'Pria','Wanita'=>'Wanita')); ?>

            <?= $form->field($model, 'tempat_lahir')->textInput(['maxlength' => true, 'required'=>true]) ?>

            <?= $form->field($model, 'tanggal_lahir')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Masukkan Tanggal Lahir ...','required'=>true],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose'=>true
                ]
            ])?>

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <a class="btn btn-danger" href="<?php echo Url::to(['pengguna/index']) ?>">Kembali</a>
    </div>

    <?php ActiveForm::end(); ?>

</div>
