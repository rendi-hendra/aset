<?php $uri = service('uri'); ?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('/') ?>">
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= ($uri->getSegment(1) == '') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('/') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item <?= ($uri->getSegment(1) == 'merk') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('merk') ?>">
            <i class="fas fa-tags"></i>
            <span>Merk</span>
        </a>
    </li>

    <li class="nav-item <?= ($uri->getSegment(1) == 'jenis') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('jenis') ?>">
            <i class="fas fa-list"></i>
            <span>Jenis</span>
        </a>
    </li>

    <li class="nav-item <?= ($uri->getSegment(1) == 'lokasi') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('lokasi') ?>">
            <i class="fas fa-map-marker-alt"></i>
            <span>Lokasi</span>
        </a>
    </li>

    <li class="nav-item <?= ($uri->getSegment(1) == 'vendors') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('vendors') ?>">
            <i class="fas fa-building"></i>
            <span>Vendor</span>
        </a>
    </li>

    <li class="nav-item <?= ($uri->getSegment(1) == 'aset') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('aset') ?>">
            <i class="fas fa-box"></i>
            <span>Aset</span>
        </a>
    </li>

    <li class="nav-item <?= ($uri->getSegment(1) == 'service') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('service') ?>">
            <i class="fas fa-tools"></i>
            <span>Service</span>
        </a>
    </li>
    
    <li class="nav-item <?= ($uri->getSegment(1) == 'users') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('users') ?>">
            <i class="bi bi-person-fill"></i>
            <span>User</span>
        </a>
    </li>
    

    <li class="nav-item <?= ($uri->getSegment(1) == 'mutasi-aset') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('mutasi-aset') ?>">
            <i class="fas fa-exchange-alt"></i>
            <span>Mutasi Aset</span>
        </a>
    </li>


    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>