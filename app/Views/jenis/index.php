<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-gray-800 m-0">JENIS ASET</h4>

    <div class="d-flex" style="gap:10px;">
      <button type="button" class="btn btn-outline-primary btn-sm" id="btnBaru">
        <i class="fas fa-plus mr-1"></i> BARU
      </button>

      <button type="submit" form="formJenis" class="btn btn-primary btn-sm">
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

          <form id="formJenis" method="post" action="<?= base_url('jenis/save') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="jenisid" id="jenisid">

            <div class="form-group">
              <label>Kode</label>
              <input type="text" name="kode" id="kode" maxlength="10"
                class="form-control" required>
            </div>

            <div class="form-group mb-0">
              <label>Jenis Aset</label>
              <input type="text" name="jenis_aset" id="jenis_aset"
                maxlength="50"
                class="form-control" required>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered table-hover mb-0" id="tblJenis">
        <thead class="bg-light">
          <tr>
            <th style="width:100px;">Kode</th>
            <th>Jenis Aset</th>
            <th style="width:120px;">Status</th>
            <th style="width:180px;">Dibuat Oleh</th>
            <th style="width:180px;">Diubah Oleh</th>
            <th style="width:180px;">Dihapus Oleh</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($jenis)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada data.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($jenis as $j): ?>
              <?php
              $statusText  = ((int)$j['isdeleted'] === 0) ? 'AKTIF' : 'TIDAK AKTIF';
              $createdDate = !empty($j['createddate']) ? date('Y-m-d', strtotime($j['createddate'])) : '';
              $dibuatOleh  = $j['createdby_name'] ?? '-';
              $diubahOleh  = $j['updatedby_name'] ?? '-';
              $dihapusOleh = $j['deletedby_name'] ?? '-';
              ?>
              <tr class="row-jenis"
                data-id="<?= $j['jenisid'] ?>"
                data-kode="<?= esc($j['jeniskode']) ?>"
                data-jenis="<?= esc($j['jenis']) ?>">
                <td><?= esc($j['jeniskode']) ?></td>
                <td><?= esc($j['jenis']) ?></td>
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

  <!-- DELETE FORM -->
  <form id="formDelete" method="post" action="<?= base_url('jenis/delete') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="jenisid" id="del_jenisid">
  </form>

</div>

<script>
  (function() {
    const btnBaru = document.getElementById('btnBaru');
    const btnHapus = document.getElementById('btnHapus');

    const jenisid = document.getElementById('jenisid');
    const kode = document.getElementById('kode');
    const jenis = document.getElementById('jenis_aset');
    const del_id = document.getElementById('del_jenisid');

    function resetForm() {
      jenisid.value = '';
      kode.value = '';
      jenis.value = '';
      del_id.value = '';
      btnHapus.disabled = true;

      document.querySelectorAll('#tblJenis tbody tr')
        .forEach(tr => tr.classList.remove('table-active'));
    }

    btnBaru.addEventListener('click', resetForm);

    document.querySelectorAll('.row-jenis').forEach(tr => {
      tr.addEventListener('click', () => {

        document.querySelectorAll('#tblJenis tbody tr')
          .forEach(x => x.classList.remove('table-active'));

        tr.classList.add('table-active');

        jenisid.value = tr.dataset.id;
        kode.value = tr.dataset.kode;
        jenis.value = tr.dataset.jenis;

        del_id.value = tr.dataset.id;
        btnHapus.disabled = false;
      });
    });
  })();
</script>

<?= $this->endSection() ?>