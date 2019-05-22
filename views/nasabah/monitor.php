<?php
use yii\helpers\Url;
$this->title = 'Monitoring Nasabah '.$model->nama.', Tanggal dan waktu '.$model->tanggal_waktu_posisi;
?>

<p>
    <a class="btn btn-danger" href="<?php echo Url::to(['nasabah/index']) ?>">Kembali</a>
</p>

<div class="box box-info">
	<div class="box-body">
		<iframe
		width="1240"
		height="600"
		frameborder="0" style="border:0"
		src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDWq4RVR_q0blB8cM_ujOIY8NrsT_8Fdcg&q=<?=$model->latitude?>,<?=$model->longitude?>">
		</iframe>	
	</div>
</div>
