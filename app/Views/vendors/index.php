<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-gray-800 m-0">VENDOR</h4>

    <div class="d-flex" style="gap:10px;">
      <button type="button" class="btn btn-outline-primary btn-sm" id="btnBaru">
        <i class="fas fa-plus mr-1"></i> BARU
      </button>

      <button type="submit" form="formVendor" class="btn btn-primary btn-sm">
        <i class="fas fa-save mr-1"></i> SIMPAN
      </button>

      <button type="submit" form="formDelete" class="btn btn-danger btn-sm" id="btnHapus" disabled>
        <i class="fas fa-trash mr-1"></i> HAPUS
      </button>
    </div>
  </div>

  <!-- FLASH -->
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>

  <!-- FORM -->
  <div class="row mb-4">
    <div class="col-lg-6 col-md-8">
      <div class="card shadow-sm">
        <div class="card-body">

          <form id="formVendor" method="post" action="<?= base_url('vendors/save') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="vendorid" id="vendorid">

            <div class="form-group">
              <label>Nama Vendor</label>
              <input type="text" name="vendor" id="vendor"
                maxlength="100"
                class="form-control" required>
            </div>

            <div class="form-group mb-0">
              <label>Alamat</label>
              <textarea name="alamat" id="alamat"
                maxlength="300"
                class="form-control"
                rows="3"></textarea>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered table-hover mb-0" id="tblVendor">
        <thead class="bg-light">
          <tr>
            <th>Vendor</th>
            <th>Alamat</th>
            <th style="width:120px;">Status</th>
            <th style="width:180px;">Dibuat Oleh</th>
            <th style="width:180px;">Diubah Oleh</th>
            <th style="width:180px;">Dihapus Oleh</th>
          </tr>
        </thead>
        <tbody>
        <tbody>
          <?php if (empty($vendor)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada data.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($vendor as $v): ?>
              <?php
              $statusText  = ((int)$v['isdeleted'] === 0) ? 'Aktif' : 'Tidak Aktif';
              $createdDate = !empty($v['createddate'])
                ? date('Y-m-d', strtotime($v['createddate']))
                : '';
              ?>
              <tr class="row-vendor"
                data-id="<?= $v['vendorid'] ?>"
                data-vendor="<?= esc($v['vendor']) ?>"
                data-alamat="<?= esc($v['alamat']) ?>">

                <td><?= esc($v['vendor']) ?></td>
                <td><?= esc($v['alamat']) ?></td>
                <td><?= esc($statusText) ?></td>
                <td><?= esc($createdDate) ?> <?= esc($v['createdby_name'] ?? '-') ?></td>
                <td><?= esc($v['updatedby_name'] ?? '-') ?></td>
                <td><?= esc($v['deletedby_name'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
        </tbody>
      </table>
    </div>
  </div>

  <!-- DELETE FORM -->
  <form id="formDelete" method="post" action="<?= base_url('vendors/delete') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="vendorid" id="del_vendorid">
  </form>

</div>

<script>
  (function() {
    const btnBaru = document.getElementById('btnBaru');
    const btnHapus = document.getElementById('btnHapus');

    const vendorid = document.getElementById('vendorid');
    const vendor = document.getElementById('vendor');
    const alamat = document.getElementById('alamat');
    const del_id = document.getElementById('del_vendorid');

    function resetForm() {
      vendorid.value = '';
      vendor.value = '';
      alamat.value = '';
      del_id.value = '';
      btnHapus.disabled = true;

      document.querySelectorAll('#tblVendor tbody tr')
        .forEach(tr => tr.classList.remove('table-active'));
    }

    btnBaru.addEventListener('click', resetForm);

    document.querySelectorAll('.row-vendor').forEach(tr => {
      tr.addEventListener('click', () => {

        document.querySelectorAll('#tblVendor tbody tr')
          .forEach(x => x.classList.remove('table-active'));

        tr.classList.add('table-active');

        vendorid.value = tr.dataset.id;
        vendor.value = tr.dataset.vendor;
        alamat.value = tr.dataset.alamat;

        del_id.value = tr.dataset.id;
        btnHapus.disabled = false;
      });
    });
  })();
</script>

<?= $this->endSection() ?>