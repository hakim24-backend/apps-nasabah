<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pencicilan */

$this->title = 'View Cicilan Nasabah';
$this->params['breadcrumbs'][] = ['label' => 'View Cicilan Nasabah', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
function to_rp($val)
{
    return "Rp " . number_format($val,0,',','.');
}
?>
<div class="pencicilan-view">

    <p>
        <a class="btn btn-danger" href="<?php echo Yii::$app->request->referrer ?>">Kembali</a>
    </p>

    <div class="box box-info">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    // 'id',
                    // 'id_peminjaman',
                    [
                    'attribute' => 'nominal_cicilan',
                    'value' => function($model){
                        return to_rp($model->nominal_cicilan);
                    }
                    ],
                    'tanggal_waktu_cicilan',
                    // 'id_pengguna',
                    [
                    'attribute' => 'id_jenis_peminjaman',
                    'value' => function($model){
                        if ($model->id_jenis_pencicilan == 1) {
                            return 'Sesuai durasi';
                        } else {
                            return 'Langsung lunas';
                        }
                    }
                    ],
                ],
            ]) ?>  
        </div>
    </div>

</div>
