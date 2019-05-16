<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Nasabah */

$this->title = 'Tambah Nasabah';
$this->params['breadcrumbs'][] = ['label' => 'Nasabahs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nasabah-create">

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
