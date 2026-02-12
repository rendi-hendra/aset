<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800"><?= isset($lokasi) ? 'Edit Lokasi' : 'Tambah Lokasi' ?></h1>

  <div class="card shadow mb-4">
    <div class="card-body">
      <form method="post" action="<?= isset($lokasi)
          ? base_url('lokasi/update/'.$lokasi['lokasiid'])
          : base_url('lokasi/store') ?>">

        <?= csrf_field() ?>

        <div class="form-group">
          <label>Kode Lokasi</label>
          <input type="text" name="lokasikode" class="form-control" maxlength="5"
                 value="<?= isset($lokasi) ? esc($lokasi['lokasikode']) : '' ?>"
                 required>
        </div>

        <div class="form-group">
          <label>Nama Lokasi</label>
          <input type="text" name="lokasi" class="form-control" maxlength="20"
                 value="<?= isset($lokasi) ? esc($lokasi['lokasi']) : '' ?>"
                 required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('lokasi') ?>" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>