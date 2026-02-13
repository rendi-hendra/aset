<?= $this->include('layouts/header') ?>
<main>
    <?= $this->renderSection('content') ?>
</main>
<?= $this->include('layouts/footer') ?>

<!-- jQuery -->

<!-- DataTables -->
<script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
<script src="<?= base_url('vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>