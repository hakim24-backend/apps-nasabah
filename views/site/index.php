<?php

/* @var $this yii\web\View */

$this->title = 'Web Admin Pinjaman Online';

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Selamat Datang <?=Yii::$app->user->identity->nama?></h1>
        <p class="lead">Fitur Utama</p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Master Nasabah</h2>

                <p>Pada fitur ini admin bisa melihat data-data yang dimiliki oleh nasabah. Admin bisa membuat, mengedit dan menghapus data nasabah sesuai kebutuhan</p>
            </div>
            <div class="col-lg-4">
                <h2>Monitoring Nasabah</h2>

                <p>Fitur ini digunakan untuk memantau nasabah yang status peminjaman atau cicilannya belum lunas</p>
            </div>
            <div class="col-lg-4">
                <h2>Peminjaman</h2>

                <p>Fitur ini mencatat data nasabah yang ingin melakukan peminjaman uang. Syarat peminjaman uang ini yaitu harus menjadi nasabah terlebih dahulu</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <h2>Pencicilan</h2>

                <p>Fitur ini bisa memantau cicilan nasabah per periode mulai dari cicilan per bulan atau per minggu, denda, dan detail  </p>
            </div>
            <div class="col-lg-4">
                <h2>Master Pengguna</h2>

                <p>Untuk fitur ini dapat membuat akun admin lebih dari satu</p>
            </div>
        </div>

    </div>
</div>
