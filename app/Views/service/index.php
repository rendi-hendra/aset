<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-gray-800 m-0">SERVICE</h4>

    <div class="d-flex" style="gap:10px;">
      <button type="button" class="btn btn-outline-primary btn-sm" id="btnBaru">
        <i class="fas fa-plus mr-1"></i> BARU
      </button>

      <button type="submit" form="formService" class="btn btn-primary btn-sm">
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

          <form id="formService" method="post" action="<?= base_url('service/save') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="asetserviceid" id="asetserviceid" value="">

            <div class="row">

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Aset</label>
                <select class="form-control" name="asetid" id="asetid" required>
                  <option value="">-- Pilih Aset --</option>
                  <?php if (!empty($asetList)): ?>
                    <?php foreach ($asetList as $a): ?>
                      <?php
                      $label = trim(($a['asetkode'] ?? '') . ' - ' . ($a['jenis'] ?? '') . ' - ' . ($a['merk'] ?? ''));
                      ?>
                      <option value="<?= (int)$a['asetid'] ?>"><?= esc($label) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Vendor</label>
                <select class="form-control" name="vendorid" id="vendorid" required>
                  <option value="">-- Pilih Vendor --</option>
                  <?php if (!empty($vendorList)): ?>
                    <?php foreach ($vendorList as $v): ?>
                      <option value="<?= (int)$v['vendorid'] ?>"><?= esc($v['vendor']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">No Service</label>
                <input type="text"
                  class="form-control"
                  name="asetserviceno"
                  id="asetserviceno"
                  maxlength="20"
                  placeholder="Contoh: INV0121231"
                  required>
              </div>

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Tgl Service</label>
                <input type="date"
                  class="form-control"
                  name="asetservicedate"
                  id="asetservicedate"
                  required>
              </div>

              <div class="col-lg-4 col-md-6 mb-3">
                <label class="mb-1">Status Service</label>
                <select class="form-control" name="servicestatusid" id="servicestatusid" required>
                  <option value="">-- Pilih Status Service --</option>
                  <?php if (!empty($statusList)): ?>
                    <?php foreach ($statusList as $s): ?>
                      <option value="<?= (int)$s['servicestatusid'] ?>"><?= esc($s['servicestatus']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-lg-12 mb-3">
                <label class="mb-1">Keterangan</label>
                <textarea class="form-control"
                  name="remarks"
                  id="remarks"
                  rows="2"
                  maxlength="1000"
                  placeholder="Isi keterangan / catatan service"
                  required></textarea>
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
      <div class="table-responsive">
        <table class="table table-bordered mb-0" id="tblService" style="width:100%;">
          <thead class="bg-light">
            <tr>
              <th style="min-width:260px;">Aset</th>
              <th style="min-width:180px;">Vendor</th>
              <th style="min-width:140px;">Tgl Service</th>
              <th style="min-width:160px;">No Service</th>
              <th style="min-width:260px;">Keterangan</th>
              <th style="min-width:160px;">Status Svc</th>
              <th style="min-width:120px;">Status</th>
              <th style="min-width:180px;">Dibuat Oleh</th>
              <th style="min-width:180px;">Diubah Oleh</th>
              <th style="min-width:180px;">Dihapus Oleh</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($service)): ?>
              <tr>
                <td colspan="10" class="text-center text-muted">Belum ada data.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($service as $r): ?>
                <?php
                $statusText  = ((int)$r['isdeleted'] === 0) ? 'AKTIF' : 'TIDAK AKTIF';
                $tglSvc      = !empty($r['asetservicedate']) ? date('Y-m-d', strtotime($r['asetservicedate'])) : '';
                $createdDate = !empty($r['createddate']) ? date('Y-m-d', strtotime($r['createddate'])) : '';
                $asetLabel   = trim(($r['asetkode'] ?? '') . ' - ' . ($r['jenis'] ?? '') . ' - ' . ($r['merk'] ?? ''));
                ?>

                <tr class="row-service"
                  data-id="<?= (int)$r['asetserviceid'] ?>"
                  data-asetid="<?= (int)($r['asetid'] ?? 0) ?>"
                  data-vendorid="<?= (int)($r['vendorid'] ?? 0) ?>"
                  data-asetservicedate="<?= esc($tglSvc) ?>"
                  data-asetserviceno="<?= esc($r['asetserviceno'] ?? '') ?>"
                  data-servicestatusid="<?= (int)($r['servicestatusid'] ?? 0) ?>"
                  data-remarks="<?= esc($r['remarks'] ?? '') ?>">
                  <td class="text-nowrap"><?= esc($asetLabel) ?></td>
                  <td class="text-nowrap"><?= esc($r['vendor'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($tglSvc) ?></td>
                  <td class="text-nowrap"><?= esc($r['asetserviceno'] ?? '') ?></td>
                  <td><?= esc($r['remarks'] ?? '') ?></td>
                  <td class="text-nowrap"><?= esc($r['servicestatus'] ?? '-') ?></td>
                  <td class="text-nowrap"><?= esc($statusText) ?></td>
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
  <form id="formDelete" method="post" action="<?= base_url('service/delete') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="asetserviceid" id="del_asetserviceid" value="">
  </form>

</div>

<style>
  #tblService {
    table-layout: auto;
  }

  #tblService th,
  #tblService td {
    white-space: nowrap;
    vertical-align: middle;
  }

  #tblService tbody tr {
    cursor: pointer;
  }

  #tblService tbody tr:hover {
    background: #f8f9fc;
  }

  div.dataTables_wrapper {
    width: 100%;
  }
</style>
<?= $this->endSection() ?>

<script>
  $(document).ready(function() {

    const btnBaru = document.getElementById('btnBaru');
    const btnHapus = document.getElementById('btnHapus');

    const asetserviceid = document.getElementById('asetserviceid');
    const asetid = document.getElementById('asetid');
    const vendorid = document.getElementById('vendorid');
    const asetservicedate = document.getElementById('asetservicedate');
    const asetserviceno = document.getElementById('asetserviceno');
    const servicestatusid = document.getElementById('servicestatusid');
    const remarks = document.getElementById('remarks');

    const del_asetserviceid = document.getElementById('del_asetserviceid');

    const table = $('#tblService').DataTable({
      scrollX: true,
      responsive: false,
      autoWidth: false,
      pageLength: 10,
      order: [
        [3, 'desc']
      ], // No Service
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
      asetserviceid.value = '';
      asetid.value = '';
      vendorid.value = '';
      asetservicedate.value = '';
      asetserviceno.value = '';
      servicestatusid.value = '';
      remarks.value = '';

      del_asetserviceid.value = '';
      btnHapus.disabled = true;

      $('#tblService tbody tr').removeClass('table-active');
    }

    btnBaru.addEventListener('click', resetForm);

    $('#tblService tbody').on('click', 'tr', function() {
      $('#tblService tbody tr').removeClass('table-active');
      $(this).addClass('table-active');

      const row = $(this);

      asetserviceid.value = row.data('id') ?? '';
      asetid.value = row.data('asetid') ?? '';
      vendorid.value = row.data('vendorid') ?? '';
      asetservicedate.value = row.data('asetservicedate') ?? '';
      asetserviceno.value = row.data('asetserviceno') ?? '';
      servicestatusid.value = row.data('servicestatusid') ?? '';
      remarks.value = row.data('remarks') ?? '';

      del_asetserviceid.value = row.data('id') ?? '';
      btnHapus.disabled = !del_asetserviceid.value;
    });

  });
</script>