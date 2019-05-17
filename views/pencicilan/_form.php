<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\money\MaskMoney;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Pencicilan */
/* @var $form yii\widgets\ActiveForm */
function to_rp($val)
{
    return "Rp " . number_format($val,0,',','.');
}
?>

<div class="pencicilan-form">

    <div class="box box-info">
        <div class="box-body">
            <b>Info</b><br>
            <?php if ($info->id_jenis_peminjaman == 1) { ?>
                Jenis Peminjaman = Jaminan<br>
            <?php } else { ?>
                Jenis Peminjaman = Non-jaminan<br>
            <?php } ?>
            Denda = <?= to_rp($denda) ?><br>
            Nominal Peminjaman = <?=to_rp($info->nominal_peminjaman)?><br>
            Langsung Lunas = <?= to_rp($rumus) ?><br>
            Cicilan per bulan = <?=to_rp($info->nominal_pencicilan)?><br>
            Tanggal Jatuh Tempo = <?=date("d/m/Y", strtotime($cicilanDenda->tanggal_jatuh_tempo))?><br>
            <!-- Jatuh Tempo = <?=date("d/m/Y", strtotime($info->tanggal_waktu_pembuatan."+1 months"))?><br> -->
            <?php if ($totalCicilan == '[]' ) { ?>
                <?php if ($info->id_jenis_durasi == 1) { ?>
                    Durasi = <?=$info->durasi?> Minggu<br>
                    Total Cicilan Perminggu = 0x
                <?php } else { ?>
                    Durasi = <?=$info->durasi?> Bulan<br>
                    Total Cicilan Perbulan = 0x
                <?php } ?>
            <?php } else { ?>
                <?php if ($info->id_jenis_durasi == 1) { ?>
                    Durasi = <?=$info->durasi?> Minggu<br>
                    Total Cicilan Perminggu = <?=$totalCicilan?>x
                <?php } else { ?>
                    Durasi = <?=$info->durasi?> Bulan<br>
                    Total Cicilan Perbulan = <?=$totalCicilan?>x
                <?php } ?>
            <?php } ?> 
        </div>
    </div>

    <div class="box box-info">
        <div class="box-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'nominal_cicilan')->widget(MaskMoney::classname(), 
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

            <label>Jenis Cicilan</label>
            <?= Html::dropDownlist('cicilan',0,[1=>'Sesuai Durasi',2=>'Langsung Lunas'], ['prompt' => 'Pilih Status Peminjaman...', 'required' => true, 'class' => 'form-control', 'id' => 'cicilan', 'style' => 'width: 100%']) ?>
            <br>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <a class="btn btn-danger" href="<?= Url::to(Yii::$app->request->referrer);?>">Kembali</a>
            </div>

            <?php ActiveForm::end(); ?>  
        </div>
    </div>

</div>
