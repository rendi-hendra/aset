<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        <?= isset($user) ? 'Edit User' : 'Tambah User' ?>
    </h1>

    <div class="card shadow">
        <div class="card-body">

            <form method="post"
                action="<?= isset($user) ? base_url('users/update/'.$user['userid']) : base_url('users/store') ?>">

                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                        value="<?= isset($user) ? esc($user['username']) : '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control"
                        value="<?= isset($user) ? esc($user['nama']) : '' ?>" required>
                </div>

                <?php if (!isset($user)) : ?>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-success">
                    <?= isset($user) ? 'Update' : 'Simpan' ?>
                </button>

                <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                    Kembali
                </a>
            </form>

        </div>
    </div>
</div>

<?= $this->endSection() ?>