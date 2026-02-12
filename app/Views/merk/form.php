<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800"><?= isset($merk) ? 'Edit Merk' : 'Tambah Merk' ?></h1>

  <div class="card shadow mb-4">
    <div class="card-body">
      <form method="post" action="<?= isset($merk)
          ? base_url('merk/update/'.$merk['merkid'])
          : base_url('merk/store') ?>">

        <?= csrf_field() ?>

        <div class="form-group">
          <label>Nama Merk</label>
          <input type="text" name="merk" class="form-control"
                 value="<?= isset($merk) ? esc($merk['merk']) : '' ?>"
                 required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('merk') ?>" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>