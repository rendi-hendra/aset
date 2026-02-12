<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Lokasi</h1>
    <a href="<?= base_url('lokasi/create') ?>" class="btn btn-primary">Tambah Lokasi</a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th width="80">No</th>
              <th width="150">Kode</th>
              <th>Lokasi</th>
              <th>Created By</th>
              <th>Created Date</th>
              <th>Updated By</th>
              <th>Updated Date</th>
              <th width="150">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($lokasi)): ?>
              <tr>
                <td colspan="8" class="text-center">Belum ada data</td>
              </tr>
            <?php else: ?>
              <?php $no = 1;
              foreach ($lokasi as $l): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= esc($l['lokasikode']) ?></td>
                  <td><?= esc($l['lokasi']) ?></td>
                  <td><?= esc($l['createdby_name'] ?? '-') ?></td>
                  <td><?= esc($l['createddate'] ?? '-') ?></td>
                  <td><?= esc($l['updatedby_name'] ?? '-') ?></td>
                  <td><?= esc($l['updateddate'] ?? '-') ?></td>
                  <td>
                    <a href="<?= base_url('lokasi/edit/' . $l['lokasiid']) ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="<?= base_url('lokasi/delete/' . $l['lokasiid']) ?>"
                      class="btn btn-sm btn-danger"
                      onclick="return confirm('Hapus lokasi ini?')">Hapus</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>