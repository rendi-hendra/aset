<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-gray-800 m-0">MERK</h4>

    <div class="d-flex" style="gap:10px;">
      <button type="button" class="btn btn-outline-primary btn-sm" id="btnBaru">
        <i class="fas fa-plus mr-1"></i> BARU
      </button>

      <button type="submit" form="formMerk" class="btn btn-primary btn-sm">
        <i class="fas fa-save mr-1"></i> SIMPAN
      </button>

      <button type="submit" form="formDelete" class="btn btn-danger btn-sm" id="btnHapus" disabled>
        <i class="fas fa-trash mr-1"></i> HAPUS
      </button>
    </div>
  </div>

  <!-- FLASH MESSAGE -->
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

          <form id="formMerk" method="post" action="<?= base_url('merk/save') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="merkid" id="merkid" value="">

            <div class="form-group mb-0">
              <label class="mb-1">Merk</label>
              <input type="text"
                     class="form-control"
                     name="merk"
                     id="merk"
                     maxlength="20"
                     placeholder="Contoh: Panasonic"
                     required>
            </div>

            <small class="text-muted d-block mt-2">
              Klik baris di tabel untuk edit. Tombol <b>BARU</b> untuk reset form.
            </small>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered mb-0" id="tblMerk">
        <thead class="bg-light">
          <tr>
            <th>Merk</th>
            <th style="width:120px;">Status</th>
            <th style="width:180px;">Dibuat Oleh</th>
            <th style="width:180px;">Diubah Oleh</th>
            <th style="width:180px;">Dihapus Oleh</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($merk)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted">Belum ada data.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($merk as $m): ?>
              <?php
                $statusText  = ((int)$m['isdeleted'] === 0) ? 'Aktif' : 'Tidak Aktif';
                $createdDate = !empty($m['createddate']) ? date('Y-m-d', strtotime($m['createddate'])) : '';
                $dibuatOleh  = $m['createdby_name'] ?? '-';
                $diubahOleh  = $m['updatedby_name'] ?? '-';
                $dihapusOleh = $m['deletedby_name'] ?? '-';
              ?>
              <tr class="row-merk"
                  data-id="<?= (int)$m['merkid'] ?>"
                  data-merk="<?= esc($m['merk']) ?>">
                <td><?= esc($m['merk']) ?></td>
                <td><?= esc($statusText) ?></td>
                <td><?= esc($createdDate) ?> <?= esc($dibuatOleh) ?></td>
                <td><?= esc($diubahOleh) ?></td>
                <td><?= esc($dihapusOleh) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- DELETE FORM (HIDDEN) -->
  <form id="formDelete" method="post" action="<?= base_url('merk/delete') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="merkid" id="del_merkid" value="">
  </form>

</div>

<style>
  #tblMerk { table-layout: fixed; width: 100%; }
  #tblMerk td, #tblMerk th { vertical-align: middle; }
  #tblMerk tbody tr { cursor: pointer; }
  #tblMerk tbody tr:hover { background: #f8f9fc; }
</style>

<script>
(function(){
  const btnBaru  = document.getElementById('btnBaru');
  const btnHapus = document.getElementById('btnHapus');

  const merkid = document.getElementById('merkid');
  const merk   = document.getElementById('merk');

  const del_merkid = document.getElementById('del_merkid');

  function resetForm(){
    merkid.value = '';
    merk.value = '';
    del_merkid.value = '';
    btnHapus.disabled = true;

    document.querySelectorAll('#tblMerk tbody tr').forEach(tr => tr.classList.remove('table-active'));
  }

  btnBaru.addEventListener('click', resetForm);

  document.querySelectorAll('.row-merk').forEach(tr => {
    tr.addEventListener('click', () => {
      document.querySelectorAll('#tblMerk tbody tr').forEach(x => x.classList.remove('table-active'));
      tr.classList.add('table-active');

      merkid.value = tr.dataset.id;
      merk.value   = tr.dataset.merk;

      del_merkid.value = tr.dataset.id;
      btnHapus.disabled = false;
    });
  });
})();
</script>

<?= $this->endSection() ?>
