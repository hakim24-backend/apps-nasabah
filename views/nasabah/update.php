<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Nasabah */

$this->title = 'Update Nasabah: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nasabahs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nasabah-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
