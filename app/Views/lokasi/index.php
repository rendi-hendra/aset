<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-gray-800 m-0">LOKASI</h4>

    <div class="d-flex" style="gap:10px;">
      <button type="button" class="btn btn-outline-primary btn-sm" id="btnBaru">
        <i class="fas fa-plus mr-1"></i> BARU
      </button>

      <button type="submit" form="formLokasi" class="btn btn-primary btn-sm">
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
    <div class="col-lg-5 col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">

          <form id="formLokasi" method="post" action="<?= base_url('lokasi/save') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="lokasiid" id="lokasiid" value="">

            <div class="form-group">
              <label class="mb-1">Kode</label>
              <input type="text"
                class="form-control"
                name="lokasikode"
                id="lokasikode"
                maxlength="5"
                placeholder="Contoh: 1LT1"
                required>
            </div>

            <div class="form-group">
              <label class="mb-1">Lokasi</label>
              <input type="text"
                class="form-control"
                name="lokasi"
                id="lokasi"
                maxlength="20"
                placeholder="Contoh: Gedung 1 Lt1"
                required>
            </div>

            <small class="text-muted">
              Klik baris di tabel untuk edit. Tombol <b>BARU</b> untuk reset form.
            </small>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-bordered table-striped" id="tblLokasi" width="100%">
        <thead class="bg-light">
          <tr>
            <th style="width:100px;">Kode</th>
            <th>Lokasi</th>
            <th style="width:120px;">Status</th>
            <th style="width:180px;">Dibuat Oleh</th>
            <th style="width:180px;">Diubah Oleh</th>
            <th style="width:180px;">Dihapus Oleh</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($lokasi)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada data.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($lokasi as $r): ?>
              <?php
              $statusText  = ((int)$r['isdeleted'] === 0) ? 'Aktif' : 'Tidak Aktif';
              $createdDate = !empty($r['createddate']) ? date('Y-m-d', strtotime($r['createddate'])) : '';
              $dibuatOleh  = $r['createdby_name'] ?? '-';
              $diubahOleh  = $r['updatedby_name'] ?? '-';
              $dihapusOleh = $r['deletedby_name'] ?? '-';
              ?>
              <tr class="row-lokasi"
                data-id="<?= (int)$r['lokasiid'] ?>"
                data-kode="<?= esc($r['lokasikode']) ?>"
                data-lokasi="<?= esc($r['lokasi']) ?>">
                <td><?= esc($r['lokasikode']) ?></td>
                <td><?= esc($r['lokasi']) ?></td>
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
  <form id="formDelete" method="post" action="<?= base_url('lokasi/delete') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="lokasiid" id="del_lokasiid" value="">
  </form>

</div>

<!-- PAGE STYLE (LOCAL ONLY) -->
<style>
  #tblLokasi {
    width: 100%;
  }

  #tblLokasi td,
  #tblLokasi th {
    vertical-align: middle;
  }

  #tblLokasi td {
    word-wrap: break-word;
    white-space: normal;
  }

  #tblLokasi tbody tr {
    cursor: pointer;
  }

  #tblLokasi tbody tr:hover {
    background: #f8f9fc;
  }
</style>

<!-- PAGE SCRIPT -->
<script>
  $(document).ready(function() {

    const btnBaru = document.getElementById('btnBaru');
    const btnHapus = document.getElementById('btnHapus');

    const lokasiid = document.getElementById('lokasiid');
    const lokasikode = document.getElementById('lokasikode');
    const lokasi = document.getElementById('lokasi');
    const del_lokasiid = document.getElementById('del_lokasiid');

    // INIT DATATABLE
    const table = $('#tblLokasi').DataTable({
      responsive: true,
      autoWidth: false,
      pageLength: 10,
      order: [
        [0, 'asc']
      ],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
          first: "Awal",
          last: "Akhir",
          next: "›",
          previous: "‹"
        }
      }
    });

    function resetForm() {
      lokasiid.value = '';
      lokasikode.value = '';
      lokasi.value = '';
      del_lokasiid.value = '';
      btnHapus.disabled = true;

      $('#tblLokasi tbody tr').removeClass('table-active');
    }

    btnBaru.addEventListener('click', resetForm);

    // IMPORTANT: pakai on() karena DataTables redraw DOM
    $('#tblLokasi tbody').on('click', 'tr', function() {

      $('#tblLokasi tbody tr').removeClass('table-active');
      $(this).addClass('table-active');

      const row = $(this);

      lokasiid.value = row.data('id');
      lokasikode.value = row.data('kode');
      lokasi.value = row.data('lokasi');

      del_lokasiid.value = row.data('id');
      btnHapus.disabled = false;

    });

  });
</script>


<?= $this->endSection() ?>