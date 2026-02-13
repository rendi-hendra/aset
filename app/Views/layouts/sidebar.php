<?php $uri = service('uri'); ?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('/') ?>">
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item <?= ($uri->getSegment(1) == '') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('/') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- User -->
    <li class="nav-item <?= ($uri->getSegment(1) == 'users') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('users') ?>">
            <i class="bi bi-person-fill"></i>
            <span>User</span>
        </a>
    </li>

    <!-- Merk -->
    <li class="nav-item <?= ($uri->getSegment(1) == 'merk') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('merk') ?>">
            <i class="fas fa-tags"></i>
            <span>Merk</span>
        </a>
    </li>

    <!-- Lokasi -->
    <li class="nav-item <?= ($uri->getSegment(1) == 'lokasi') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('lokasi') ?>">
            <i class="fas fa-map-marker-alt"></i>
            <span>Lokasi</span>
        </a>
    </li>

    <!-- Jenis -->
    <li class="nav-item <?= ($uri->getSegment(1) == 'jenis') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('jenis') ?>">
            <i class="fas fa-list"></i>
            <span>Jenis</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>