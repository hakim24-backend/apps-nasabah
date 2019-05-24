<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\money\MaskMoney;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Pencicilan */

$this->title = 'Update Cicilan';
$this->params['breadcrumbs'][] = ['label' => 'Data Cicilan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
            Langsung Lunas = 
            <div id="cicilan-lunas" style="display:inline"> 
                <?= to_rp($rumus) ?>
            </div><br>
            <?php if ($info->id_jenis_durasi == 1) { ?>
                Cicilan per minggu = 
                <div id="cicilan-bulan" style="display:inline"> 
                    <?=to_rp($info->nominal_pencicilan)?>
                </div><br>
            <?php } else { ?>
                Cicilan per bulan = 
                <div id="cicilan-bulan" style="display:inline"> 
                    <?=to_rp($info->nominal_pencicilan)?>
                </div><br>
            <?php } ?>
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

            <label>Jenis Cicilan</label>
            <?= Html::dropDownlist('cicilan',$model->id_jenis_pencicilan,[1=>'Sesuai Durasi',2=>'Langsung Lunas'], ['prompt' => 'Pilih Status Peminjaman...', 'required' => true, 'class' => 'form-control', 'id' => 'cicilan', 'style' => 'width: 100%']) ?>
            <br>

            <div id="nominal_update">
                <label>Nominal Cicilan</label>
                <input type="text" readonly id="nominal_update" class="form-control" name="nominal_update" value="<?=to_rp($model->nominal_cicilan)?>"><br>
            </div>

            <div id="nominal">
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-save']) ?>
                <a class="btn btn-danger" href="<?= Url::to(Yii::$app->request->referrer);?>">Kembali</a>
            </div>

            <?php ActiveForm::end(); ?>  
        </div>
    </div>

</div>

<?php 
  
$this->registerJs("

    $('#cicilan').on('click',function(){
        var id = $('#cicilan').val();
        var denda = $('#denda').text();
        var denda_fix = denda.replace('Rp ','').replace(/\./g,'');
        var cicilan = $('#cicilan-bulan').text();
        var cicilan_fix  = cicilan.replace('Rp ','').replace(/\./g,'');
        var cicilan_lunas = $('#cicilan-lunas').text();
        var cicilan_lunas_fix  = cicilan_lunas.replace('Rp ','').replace(/\./g,'');
        var cicilan_denda = BigInt(cicilan_fix)+BigInt(denda_fix);
        var cicilan_lunas_denda = BigInt(cicilan_lunas_fix)+BigInt(denda_fix);

        $.ajax({
          url : '" . Yii::$app->urlManager->baseUrl."/pencicilan/get-nominal-cicilan?id='+id+'&cicilan_denda='+cicilan_denda+'&cicilan_lunas_denda='+cicilan_lunas_denda,
          dataType : 'html',
          success: function (data) {
            $('#nominal_update').hide();
            $('#nominal').html(data);
          }
        })

    });
 
");

?>
