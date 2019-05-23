<?php
use yii\helpers\Url;
$date=date_create($model->tanggal_waktu_posisi);
$this->title = 'Monitoring Nasabah '.$model->nama.', pada: '.date_format($date, 'd F Y H:i:s');
?>

<p>
    <a class="btn btn-danger" href="<?php echo Url::to(['monitor/index']) ?>">Kembali</a>
</p>

<div class="box box-info">
	<div class="box-body">
		<iframe
		width="100%"
		height="600"
		frameborder="0" style="border:0"
		src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDWq4RVR_q0blB8cM_ujOIY8NrsT_8Fdcg&q=<?=$model->latitude?>,<?=$model->longitude?>">
		</iframe>	
	</div>
</div>
