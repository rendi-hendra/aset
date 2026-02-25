<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-gray-800 m-0">ASET</h4>

    <div class="d-flex" style="gap:10px;">
      <button type="button" class="btn btn-outline-primary btn-sm" id="btnBaru">
        <i class="fas fa-plus mr-1"></i> BARU
      </button>

      <button type="submit" form="formAset" class="btn btn-primary btn-sm">
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
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">

          <form id="formAset" method="post" action="<?= base_url('aset/save') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="asetid" id="asetid" value="">

            <div class="row">

              <div class="col-lg-3 col-md-6 mb-3">
                <label class="mb-1">No Aset</label>
                <input type="text"
                  class="form-control"
                  name="asetkode"
                  id="asetkode"
                  maxlength="20"
                  placeholder="Kosongkan untuk auto-generate">
                <small class="text-muted">Jika dikosongkan, sistem akan buat otomatis.</small>
              </div>

              <div class="col-lg-3 col-md-6 mb-3">
                <label class="mb-1">Jenis</label>
                <select class="form-control" name="jenisid" id="jenisid" required>
                  <option value="">-- Pilih Jenis --</option>
                  <?php if (!empty($jenis)): ?>
                    <?php foreach ($jenis as $j): ?>
                      <option value="<?= (int)$j['jenisid'] ?>"><?= esc($j['jenis']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-lg-3 col-md-6 mb-3">
                <label class="mb-1">Tgl Pembelian</label>
                <input type="date"
                  class="form-control"
                  name="pembeliandate"
                  id="pembeliandate"
                  required>
              </div>

              <div class="col-lg-3 col-md-6 mb-3">
                <label class="mb-1">Merk</label>
                <select class="form-control" name="merkid" id="merkid" required>
                  <option value="">-- Pilih Merk --</option>
                  <?php if (!empty($merk)): ?>
                    <?php foreach ($merk as $m): ?>
                      <option value="<?= (int)$m['merkid'] ?>"><?= esc($m['merk']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <!-- Tambahan wajib karena DB butuh lokasiid NOT NULL -->
              <div class="col-lg-3 col-md-6 mb-3">
                <label class="mb-1">Lokasi</label>
                <select class="form-control" name="lokasiid" id="lokasiid" required>
                  <option value="">-- Pilih Lokasi --</option>
                  <?php if (!empty($lokasi)): ?>
                    <?php foreach ($lokasi as $l): ?>
                      <option value="<?= (int)$l['lokasiid'] ?>"><?= esc($l['lokasi']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-lg-3 col-md-6 mb-3">
                <label class="mb-1">No Pembelian</label>
                <input type="text"
                  class="form-control"
                  name="pembelianno"
                  id="pembelianno"
                  maxlength="20"
                  placeholder="Contoh: INV0121231"
                  required>
              </div>

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
    <div class="card-body">

      <!-- wrapper ini bikin tabel bisa scroll horizontal -->
      <div class="table-responsive">
        <table class="table table-bordered mb-0" id="tblAset" style="width:100%;">
          <thead class="bg-light">
            <tr>
              <th style="min-width:140px;">No Aset</th>
              <th style="min-width:140px;">Jenis</th>
              <th style="min-width:140px;">Merk</th>
              <th style="min-width:160px;">Lokasi</th>
              <th style="min-width:140px;">Tgl Pembelian</th>
              <th style="min-width:160px;">No Pembelian</th>
              <th style="min-width:120px;">Status</th>
              <th style="min-width:180px;">Dibuat Oleh</th>
              <th style="min-width:180px;">Diubah Oleh</th>
              <th style="min-width:180px;">Dihapus Oleh</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($aset)): ?>
              <tr>
                <td colspan="10" class="text-center text-muted">Belum ada data.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($aset as $a): ?>
                <?php
                $statusText  = ((int)$a['isdeleted'] === 0) ? 'AKTIF' : 'TIDAK AKTIF';
                $tglBeli     = !empty($a['pembeliandate']) ? date('Y-m-d', strtotime($a['pembeliandate'])) : '';

                $createdDate = !empty($a['createddate'])
                  ? date('Y-m-d H:i', strtotime($a['createddate']))
                  : '';

                $updatedDate = !empty($a['updateddate'])
                  ? date('Y-m-d H:i', strtotime($a['updateddate']))
                  : '';

                $deletedDate = !empty($a['deleteddate'])
                  ? date('Y-m-d H:i', strtotime($a['deleteddate']))
                  : '';

                $dibuatOleh  = $a['createdby_name'] ?? '-';
                $diubahOleh  = $a['updatedby_name'] ?? '-';
                $dihapusOleh = $a['deletedby_name'] ?? '-';
                ?>
                <tr class="row-aset"
                  data-id="<?= (int)$a['asetid'] ?>"
                  data-asetkode="<?= esc($a['asetkode'] ?? '') ?>"
                  data-jenisid="<?= (int)($a['jenisid'] ?? 0) ?>"
                  data-merkid="<?= (int)($a['merkid'] ?? 0) ?>"
                  data-lokasiid="<?= (int)($a['lokasiid'] ?? 0) ?>"
                  data-pembeliandate="<?= esc($tglBeli) ?>"
                  data-pembelianno="<?= esc($a['pembelianno'] ?? '') ?>">
                  <td class="text-nowrap"><?= esc($a['asetkode'] ?? '') ?></td>
                  <td class="text-nowrap"><?= esc($a['jenis'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($a['merk'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($a['lokasi'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($tglBeli) ?></td>
                  <td class="text-nowrap"><?= esc($a['pembelianno'] ?? '') ?></td>
                  <td class="text-nowrap"><?= esc($statusText) ?></td>
                  <td>
                    <?= esc($createdDate) ?><br>
                    <small class="text-muted"><?= esc($dibuatOleh) ?></small>
                  </td>

                  <td>
                    <?= esc($updatedDate) ?><br>
                    <small class="text-muted"><?= esc($diubahOleh) ?></small>
                  </td>

                  <td>
                    <?= esc($deletedDate) ?><br>
                    <small class="text-muted"><?= esc($dihapusOleh) ?></small>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- DELETE FORM (HIDDEN) -->
  <form id="formDelete" method="post" action="<?= base_url('aset/delete') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="asetid" id="del_asetid" value="">
  </form>

</div>

<style>
  /* HILANGKAN table-layout fixed yang bikin kolom kepaksa sempit */
  #tblAset {
    table-layout: auto;
  }

  /* pastikan text gak dibikin turun per huruf */
  #tblAset th {
  white-space: nowrap;
}

#tblAset td {
  vertical-align: middle;
}

  #tblAset tbody tr {
    cursor: pointer;
  }

  #tblAset tbody tr:hover {
    background: #f8f9fc;
  }

  /* DataTables scrollX kadang bikin wrapper sempit kalau ini tidak ada */
  div.dataTables_wrapper {
    width: 100%;
  }
</style>

<script>
  $(document).ready(function() {

    const btnBaru = document.getElementById('btnBaru');
    const btnHapus = document.getElementById('btnHapus');

    const asetid = document.getElementById('asetid');
    const asetkode = document.getElementById('asetkode');
    const jenisid = document.getElementById('jenisid');
    const pembeliandate = document.getElementById('pembeliandate');
    const merkid = document.getElementById('merkid');
    const lokasiid = document.getElementById('lokasiid');
    const pembelianno = document.getElementById('pembelianno');

    const del_asetid = document.getElementById('del_asetid');

    const table = $('#tblAset').DataTable({
      // kunci rapih: biarkan dia scroll horizontal, bukan maksa menyempit
      scrollX: true,
      responsive: false,
      autoWidth: false,
      pageLength: 10,
      order: [
        [0, 'desc']
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
      asetid.value = '';
      asetkode.value = '';
      jenisid.value = '';
      pembeliandate.value = '';
      merkid.value = '';
      lokasiid.value = '';
      pembelianno.value = '';

      del_asetid.value = '';
      btnHapus.disabled = true;

      $('#tblAset tbody tr').removeClass('table-active');
    }

    btnBaru.addEventListener('click', resetForm);

    $('#tblAset tbody').on('click', 'tr', function() {
      $('#tblAset tbody tr').removeClass('table-active');
      $(this).addClass('table-active');

      const row = $(this);

      asetid.value = row.data('id') ?? '';
      asetkode.value = row.data('asetkode') ?? '';
      jenisid.value = row.data('jenisid') ?? '';
      merkid.value = row.data('merkid') ?? '';
      lokasiid.value = row.data('lokasiid') ?? '';
      pembeliandate.value = row.data('pembeliandate') ?? '';
      pembelianno.value = row.data('pembelianno') ?? '';

      del_asetid.value = row.data('id') ?? '';
      btnHapus.disabled = !del_asetid.value;
    });

  });
</script>

<?= $this->endSection() ?>