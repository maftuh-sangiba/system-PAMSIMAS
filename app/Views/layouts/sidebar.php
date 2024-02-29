<?php
$uri = service('uri')->getSegments(1);
$currentUri = $uri[0];
?>

<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="<?= base_url() ?>dashboard">Admin PAMSIMAS</a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item <?= ($currentUri == 'dashboard') ? 'active' : '' ?> ">
                    <a href="<?= base_url() ?>dashboard" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item <?= ($currentUri == 'pelanggan') ? 'active' : '' ?>">
                    <a href="<?= base_url() ?>pelanggan" class='sidebar-link'>
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                        <span>Data Pelanggan</span>
                    </a>
                </li>

                <li class="sidebar-item <?= ($currentUri == 'meteran') ? 'active' : '' ?>">
                    <a href="/meteran" class='sidebar-link'>
                        <i class="bi bi-speedometer"></i>
                        <span>Data Meteran</span>
                    </a>
                </li>

                <li class="sidebar-item <?= ($currentUri == 'penggunaan') ? 'active' : '' ?>">
                    <a href="<?= base_url() ?>penggunaan" class='sidebar-link'>
                        <i class="bi bi-droplet-half"></i>
                        <span>Data Penggunaan Air</span>
                    </a>
                </li>

                <li class="sidebar-item <?= ($currentUri == 'pembayaran') ? 'active' : '' ?>">
                    <a href="<?= base_url() ?>pembayaran" class='sidebar-link'>
                        <i class="bi bi-cash-stack"></i>
                        <span>Data Pembayaran</span>
                    </a>
                </li>

                <li class="sidebar-item <?= ($currentUri == 'saldo') ? 'active' : '' ?>">
                    <a href="<?= base_url() ?>saldo" class='sidebar-link'>
                        <i class="bi bi-wallet2"></i>
                        <span>Data Saldo</span>
                    </a>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>