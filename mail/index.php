<?php 
use yii\helpers\Html;
use yii\helpers\Url;

 ?>

Selamat! Proses awal registrasi telah berhasil. Silakan klik link berikut ini untuk memverifikasi email Anda:
<br/><br/><br/>

<?= 
// Html::a(Html::button('Konfirmasi Akun', ['style' => 'width: 150px;height: 35px;background: #355438;border: none;color: white;']),Yii::$app->urlManager->createAbsoluteUrl(['site/confirm','access_token'=>$access_token])) 
Html::a(Html::button('Konfirmasi Akun', ['style' => 'width: 150px;height: 35px;background: #355438;border: none;color: white;']),'http://192.168.100.6/pinjaman-online/web/site/confirm?access_token='.$access_token) 
?>

<br/>
<br/><br/>
Regards,
Admin Pinjaman Online