<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Nasabah */

$this->title = 'View Nasabah';
$this->params['breadcrumbs'][] = ['label' => 'Nasabahs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="nasabah-view">

    <p>
        <a class="btn btn-warning" href="<?php echo Url::to(['nasabah/index']) ?>">Kembali</a>
        <?php if ($akun->id_status_akun == 2) { ?>
            <?= Html::a('Aktifkan Akun', ['aktif-akun', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php } else { ?>
            <?= Html::a('Non-Aktifkan Akun', ['non-aktif-akun', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
        <?php } ?>
    </p>

    <div class="box box-info">
        <div class="box-body">
             <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'nama',
                        'alamat',
                        'tempat_lahir',
                        'tanggal_lahir',
                        'jenis_kelamin',
                        'nomor_telepon',
                        'email:email',
                        [
                            'attribute' => 'foto_ktp',
                            'format'=>'html',
                            'value'=>function($data){
                                return Html::img('../../web/foto/'.$data['foto_ktp'],['width' => '150px']);
                            }
                        ],
                        [
                            'attribute' => 'foto_bersama_ktp',
                            'format'=>'html',
                            'value'=>function($data){
                                return Html::img('../../web/foto/'.$data['foto_bersama_ktp'],['width' => '150px']);
                            }
                        ],
                        [
                            'attribute' => 'latitude',
                            'value' => $model->latitude != null ? $model->latitude : 'Belum Ada'
                        ],
                        [
                            'attribute' => 'longitude',
                            'value' => $model->longitude != null ? $model->longitude : 'Belum Ada'
                        ],
                    ],
                ]) ?>
        </div>
    </div>

</div>
