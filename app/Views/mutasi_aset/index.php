<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-gray-800 m-0">MUTASI ASET</h4>

    <div class="d-flex" style="gap:10px;">
      <button type="button" class="btn btn-outline-primary btn-sm" id="btnBaru">
        <i class="fas fa-plus mr-1"></i> BARU
      </button>

      <button type="submit" form="formMutasi" class="btn btn-primary btn-sm">
        <i class="fas fa-save mr-1"></i> SIMPAN
      </button>

      <button type="submit" form="formDelete" class="btn btn-danger btn-sm" id="btnHapus" disabled>
        <i class="fas fa-trash mr-1"></i> HAPUS
      </button>

      <button type="submit" form="formFilter" class="btn btn-secondary btn-sm">
        <i class="fas fa-search mr-1"></i> CEK
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

  <!-- FORM INPUT MUTASI -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">

          <form id="formMutasi" method="post" action="<?= base_url('mutasi-aset/save') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="asetmoveid" id="asetmoveid" value="">

            <div class="row">
              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Aset</label>
                <select class="form-control" name="asetid_form" id="asetid_form" required>
                  <option value="">-- Pilih Aset --</option>
                  <?php if (!empty($asetList)): ?>
                    <?php foreach ($asetList as $a): ?>
                      <?php $label = trim(($a['asetkode'] ?? '') . ' - ' . ($a['jenis'] ?? '') . ' - ' . ($a['merk'] ?? '')); ?>
                      <option value="<?= (int)$a['asetid'] ?>"
                        data-current-lokasiid="<?= (int)($a['lokasiid'] ?? 0) ?>">
                        <?= esc($label) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <small class="text-muted">Lokasi awal otomatis dari lokasi terakhir aset.</small>
              </div>

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Lokasi Awal (Otomatis)</label>
                <select class="form-control" id="lokasiawalid" disabled>
                  <option value="">-- Otomatis --</option>
                  <?php if (!empty($lokasiList)): ?>
                    <?php foreach ($lokasiList as $l): ?>
                      <option value="<?= (int)$l['lokasiid'] ?>"><?= esc($l['lokasi']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Lokasi Akhir</label>
                <select class="form-control" name="lokasiakhirid" id="lokasiakhirid" required>
                  <option value="">-- Pilih Lokasi Akhir --</option>
                  <?php if (!empty($lokasiList)): ?>
                    <?php foreach ($lokasiList as $l): ?>
                      <option value="<?= (int)$l['lokasiid'] ?>"><?= esc($l['lokasi']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <small class="text-muted d-block mt-2">
              Klik baris di tabel untuk edit Lokasi Akhir. Tombol <b>BARU</b> untuk reset form.
            </small>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- FILTER (CEK) -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <form id="formFilter" method="get" action="<?= base_url('mutasi-aset') ?>">
            <div class="row">
              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Aset</label>
                <select class="form-control" name="asetid" id="asetid">
                  <option value="">-- Semua Aset --</option>
                  <?php if (!empty($asetList)): ?>
                    <?php foreach ($asetList as $a): ?>
                      <?php
                        $label = trim(($a['asetkode'] ?? '') . ' - ' . ($a['jenis'] ?? '') . ' - ' . ($a['merk'] ?? ''));
                        $selected = ((int)($filter['asetid'] ?? 0) === (int)$a['asetid']) ? 'selected' : '';
                      ?>
                      <option value="<?= (int)$a['asetid'] ?>" <?= $selected ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Tgl Awal</label>
                <input type="date" class="form-control" name="start" id="start" value="<?= esc($filter['start'] ?? '') ?>">
              </div>

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Tgl Akhir</label>
                <input type="date" class="form-control" name="end" id="end" value="<?= esc($filter['end'] ?? '') ?>">
              </div>
            </div>

            <small class="text-muted d-block">
              Filter lalu klik <b>CEK</b> untuk refresh tabel mutasi.
            </small>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered mb-0" id="tblMutasi" style="width:100%;">
          <thead class="bg-light">
            <tr>
              <th style="min-width:160px;">No Mutasi</th>
              <th style="min-width:140px;">Tgl Mutasi</th>
              <th style="min-width:280px;">Aset</th>
              <th style="min-width:160px;">Reff No</th>
              <th style="min-width:180px;">Lokasi Awal</th>
              <th style="min-width:180px;">Lokasi Akhir</th>
              <th style="min-width:180px;">Dibuat Oleh</th>
              <th style="min-width:180px;">Diubah Oleh</th>
              <th style="min-width:180px;">Dihapus Oleh</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($rows)): ?>
              <tr>
                <td colspan="9" class="text-center text-muted">Belum ada data.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($rows as $r): ?>
                <?php
                  $tglMutasi   = !empty($r['createddate']) ? date('Y-m-d', strtotime($r['createddate'])) : '';
                  $createdDate = $tglMutasi;
                  $asetLabel   = trim(($r['asetkode'] ?? '') . ' - ' . ($r['jenis'] ?? '') . ' - ' . ($r['merk'] ?? ''));
                ?>
                <tr class="row-mutasi"
                  data-id="<?= (int)$r['asetmoveid'] ?>"
                  data-asetid="<?= (int)($r['asetid'] ?? 0) ?>"
                  data-lokasiawalid="<?= (int)($r['lokasiawalid'] ?? 0) ?>"
                  data-lokasiakhirid="<?= (int)($r['lokasiakhirid'] ?? 0) ?>"
                >
                  <td class="text-nowrap"><?= esc($r['asetmoveno'] ?? '') ?></td>
                  <td class="text-nowrap"><?= esc($tglMutasi) ?></td>
                  <td class="text-nowrap"><?= esc($asetLabel) ?></td>
                  <td class="text-nowrap"><?= esc($r['pembelianno'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($r['lokasi_awal'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($r['lokasi_akhir'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($createdDate) ?> <?= esc($r['createdby_name'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($r['updatedby_name'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($r['deletedby_name'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- DELETE FORM (HIDDEN) -->
  <form id="formDelete" method="post" action="<?= base_url('mutasi-aset/delete') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="asetmoveid" id="del_asetmoveid" value="">
  </form>

</div>

<style>
  #tblMutasi { table-layout: auto; }
  #tblMutasi th, #tblMutasi td { white-space: nowrap; vertical-align: middle; }
  #tblMutasi tbody tr { cursor: pointer; }
  #tblMutasi tbody tr:hover { background: #f8f9fc; }
  div.dataTables_wrapper { width: 100%; }
</style>

<script>
  $(document).ready(function() {

    const btnBaru = document.getElementById('btnBaru');
    const btnHapus = document.getElementById('btnHapus');

    const asetmoveid = document.getElementById('asetmoveid');
    const asetid_form = document.getElementById('asetid_form');
    const lokasiawalid = document.getElementById('lokasiawalid'); // disabled tapi tetap bisa diset via JS
    const lokasiakhirid = document.getElementById('lokasiakhirid');

    const del_asetmoveid = document.getElementById('del_asetmoveid');

    $('#tblMutasi').DataTable({
      scrollX: true,
      responsive: false,
      autoWidth: false,
      pageLength: 10,
      order: [[1, 'desc']],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
      }
    });

    function resetForm(){
      asetmoveid.value = '';
      asetid_form.value = '';
      lokasiawalid.value = '';
      lokasiakhirid.value = '';
      del_asetmoveid.value = '';
      btnHapus.disabled = true;

      asetid_form.disabled = false;
      lokasiakhirid.disabled = false;

      $('#tblMutasi tbody tr').removeClass('table-active');
    }

    btnBaru.addEventListener('click', resetForm);

    // Auto isi lokasi awal dari lokasi terakhir aset (lokasiid di tabel aset)
    asetid_form.addEventListener('change', function(){
      const opt = asetid_form.options[asetid_form.selectedIndex];
      const currentLokasiId = opt ? (opt.getAttribute('data-current-lokasiid') || '') : '';
      lokasiawalid.value = currentLokasiId || '';
    });

    // Klik row => edit (aset & lokasi awal dikunci)
    $('#tblMutasi tbody').on('click', 'tr', function(){
      $('#tblMutasi tbody tr').removeClass('table-active');
      $(this).addClass('table-active');

      const row = $(this);

      asetmoveid.value = row.data('id') ?? '';
      asetid_form.value = row.data('asetid') ?? '';
      lokasiawalid.value = row.data('lokasiawalid') ?? '';
      lokasiakhirid.value = row.data('lokasiakhirid') ?? '';

      del_asetmoveid.value = row.data('id') ?? '';
      btnHapus.disabled = !del_asetmoveid.value;

      // kunci aset & lokasi awal (histori), boleh edit lokasi akhir
      asetid_form.disabled = true;
    });

  });
</script>

<?= $this->endSection() ?>