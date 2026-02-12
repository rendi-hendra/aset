<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Divider -->
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Information -->
        <li class="nav-item dropdown no-arrow">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle"
                href="#"
                id="userDropdown"
                role="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">

                <!-- Nama user dari session -->
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?= session()->get('nama'); ?>
                </span>

                <!-- Avatar -->
                <img class="img-profile rounded-circle"
                    src="<?= base_url('img/undraw_profile.svg') ?>">
            </a>

            <!-- Dropdown -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">

                <div class="dropdown-header text-gray-600">
                    Login sebagai:
                    <br>
                    <strong><?= session()->get('username'); ?></strong>
                </div>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="<?= base_url('logout'); ?>">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>

            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->