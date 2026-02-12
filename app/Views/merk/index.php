<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Merk</h1>
    <a href="<?= base_url('merk/create') ?>" class="btn btn-primary">Tambah Merk</a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th width="60">No</th>
              <th>Merk</th>
              <th>Created By</th>
              <th>Created Date</th>
              <th>Updated By</th>
              <th>Updated Date</th>
              <th width="150">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($merk)): ?>
              <tr><td colspan="9" class="text-center">Belum ada data</td></tr>
            <?php else: ?>
              <?php $no=1; foreach($merk as $m): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= esc($m['merk']) ?></td>

                  <td><?= esc($m['createdby_name'] ?? '-') ?></td>
                  <td><?= esc($m['createddate'] ?? '-') ?></td>

                  <td><?= esc($m['updatedby_name'] ?? '-') ?></td>
                  <td><?= esc($m['updateddate'] ?? '-') ?></td>

                  <td>
                    <a href="<?= base_url('merk/edit/'.$m['merkid']) ?>" 
                       class="btn btn-sm btn-warning">Edit</a>

                    <a href="<?= base_url('merk/delete/'.$m['merkid']) ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Hapus merk ini?')">Hapus</a>
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