<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\file\FileInput;
use kartik\select2\Select2;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Peminjaman */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="peminjaman-form">

    <div class="box box-info">
        <div class="box-body">
            <?php $form = ActiveForm::begin(); ?>

            <!-- <?= $form->field($model, 'nama')->textInput(['maxlength' => true]) ?> -->

            <?php echo $form->field($model, 'tanggal_waktu_pembuatan')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Masukkan tanggal ...','required'=>true],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ]
            ])?>

            <?= $form->field($model, 'nama')->widget(Select2::classname(), [
                'data' => $nama,
                'options' => [
                    'placeholder' => 'Pilih Nasabah...', 
                    'id'=>'email',
                    'required'=>true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'initialize' => true,
                ],
            ]) ?>

            <!-- <?= $form->field($model, 'alamat')->textInput(['maxlength' => true,'required'=>true]) ?> -->

            <?= $form->field($model, 'nik_ktp')->textInput(['maxlength' => true,'required'=>true]) ?>

            <?= $form->field($model, 'nominal_peminjaman')->widget(MaskMoney::classname(), 
              [
                'options' => [
                    'required'=>true
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

            <label>Status Peminjaman</label>
            <?= Html::dropDownlist('status',0,[1=>'Jaminan',2=>'Non-Jaminan'], ['prompt' => 'Pilih Status Peminjaman...', 'required' => true, 'class' => 'form-control', 'id' => 'status', 'style' => 'width: 100%']) ?>
            <br>

            <div id="jenis">
            </div>

            <div id="durasi">
            </div>

            <label>Jenis Durasi</label>
            <?= Html::dropDownlist('jenis-durasi',0,[1=>'Mingguan',2=>'Bulanan'], ['prompt' => 'Pilih Jenis Durasi...', 'required' => true, 'class' => 'form-control', 'id' => 'jenis-durasi', 'style' => 'width: 100%']) ?>
            <br>

            <?= $form->field($model, 'durasi')->textInput(['maxlength' => true,'required' => true]) ?>

            <?= $form->field($model, 'foto_optional')->widget(FileInput::classname(), [
                'options' => [
                    'accept' => 'image/*'
                ],
                'pluginOptions' => [
                    // 'showPreview' => false,
                    // 'showCaption' => true,
                    // 'showRemove' => true,
                    'removeClass' => 'btn btn-danger',
                    'showUpload' => false,
                    'removeIcon' => '<i class="glyphicon glyphicon-trash"></i>'
                ],
            ]) ?>

            <div id="nasabah">
            </div>

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
            var id = $('#status').val();
            $.ajax({
              url : '" . Yii::$app->urlManager->baseUrl."/peminjaman/get-status?id='+id,
              dataType : 'html',
              success: function (data) {
                $('#jenis').html(data);
              }
            })
        });

        $('#jenis-durasi').on('change',function(){
            var id_status = $('#status').val();
            var id_durasi = $('#jenis-durasi').val();
            if(id_status == 2){
                $.ajax({
                  url : '" . Yii::$app->urlManager->baseUrl."/peminjaman/get-durasi?id_durasi='+id_durasi,
                  dataType : 'html',
                  success: function (data) {
                    $('#durasi').html(data);
                  }
                })
            }
        });

        $('#email').on('change',function(){
            var id = $('#email').val();
            $.ajax({
              url : '" . Yii::$app->urlManager->baseUrl."/peminjaman/get-nasabah?id='+id,
              dataType : 'html',
              success: function (data) {
                $('#nasabah').html(data);
              }
            })
        });

    ")
?>
