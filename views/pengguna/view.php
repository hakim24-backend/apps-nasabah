<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pengguna */

$this->title = 'View Data Pengguna';
$this->params['breadcrumbs'][] = ['label' => 'Penggunas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pengguna-view">

    <p>
        <a class="btn btn-danger" href="<?php echo Url::to(['pengguna/index']) ?>">Kembali</a>
    </p>

    <div class="box box-info">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    // 'id',
                    'nama',
                    'alamat',
                    'jenis_kelamin',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'email:email',
                    // 'id_akun',
                ],
            ]) ?>
        </div>
    </div>

    

</div>
