<?php
$akun = Yii::$app->user->identity->roleAkun();
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?=Yii::getAlias('@web')?>/logo/bossq.jpeg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><!--  --></p>
                <small>Pinjaman Online</small>
            </div>
        </div>

        <?php if ($akun == 3 || $akun == 4) { ?>
            <?= dmstr\widgets\Menu::widget(
            [
                    'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                    'items' => [
                        ['label' => 'Menu Pinjaman Online', 'options' => ['class' => 'header']],
                        // ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                        ['label' => 'Master Nasabah', 'icon' => 'users', 'url' => ['/nasabah/index']],
                        ['label' => 'Monitoring Nasabah', 'icon' => 'map-marker', 'url' => ['/monitor/index']],
                        ['label' => 'Peminjaman', 'icon' => 'credit-card', 'url' => ['/peminjaman/index']],
                        ['label' => 'Pencicilan', 'icon' => 'balance-scale', 'url' => ['/pencicilan/index']],
                        ['label' => 'Master Pengguna', 'icon' => 'user', 'url' => ['/pengguna/index']],
                        ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    ],
                ]
            ) ?>
        <?php } else { ?>
            <?= dmstr\widgets\Menu::widget(
            [
                    'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                    'items' => [
                        ['label' => 'Menu Pinjaman Online', 'options' => ['class' => 'header']],
                        // ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                        ['label' => 'Master Nasabah', 'icon' => 'users', 'url' => ['/nasabah/index']],
                        ['label' => 'Monitoring Nasabah', 'icon' => 'map-marker', 'url' => ['/monitor/index']],
                        ['label' => 'Peminjaman', 'icon' => 'credit-card', 'url' => ['/peminjaman/index']],
                        ['label' => 'Pencicilan', 'icon' => 'balance-scale', 'url' => ['/pencicilan/index']],
                        ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    ],
                ]
            ) ?>
        <?php } ?>

    </section>

</aside>
