<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini"></span><span class="logo-lg">Pinjaman Online</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <!-- User Account: style can be found in dropdown.less -->

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?=Yii::getAlias('@web')?>/logo/bossq.jpeg" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?=Yii::getAlias('@web')?>/logo/bossq.jpeg" class="img-circle"
                                 alt="User Image"/>

                            <p>
                                
                                <small>Pinjaman Online</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?php 
                                    $url = Url::toRoute(['/site/reset-password', 'id'=>Yii::$app->user->identity->id]);
                                    echo Html::button('Ubah Password', ['value'=>$url,'class' => 'btn btn-default modalButton']);
                                ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<?php

    Modal::begin([
        'header' => 'Ubah Password',
        'id' => 'modal',
        'size' => 'modal-md',
    ]);
    echo "<div id='modalContent'></div>";
    Modal::end();

    $this->registerJs("
        $('.modalButton').on('click', function () {
            $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
        });
    ");
?>
