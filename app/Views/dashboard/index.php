<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>

    <!-- FILTER TANGGAL -->
    <form method="get" action="<?= base_url('dashboard') ?>" class="d-flex align-items-center" style="gap:10px;">
      <label class="mb-0 text-muted" for="date">Tanggal</label>
      <input
        type="date"
        class="form-control form-control-sm"
        id="date"
        name="date"
        value="<?= esc($selectedDate ?? date('Y-m-d')) ?>"
        style="max-width: 180px;"
      >
      <button class="btn btn-sm btn-primary" type="submit">
        Tampilkan
      </button>
    </form>
  </div>

  <!-- INFO CARDS (USER + WELCOME) -->
  <div class="row mb-4">

    <!-- Total User -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Total User
              </div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?= $totalUser ?? 0 ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-users fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Welcome -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                Selamat Datang
              </div>
              <div class="h6 mb-0 font-weight-bold text-gray-800">
                <?= esc(session('nama')); ?>
              </div>
              <small class="text-muted">Tanggal: <?= esc($selectedDate ?? '') ?></small>
            </div>
            <div class="col-auto">
              <i class="fas fa-user-check fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- 2 BAGIAN: STATUS & LOKASI -->
  <div class="row">

    <!-- BAGIAN 1: STATUS SERVICE -->
    <div class="col-lg-8 mb-4">
      <div class="card shadow">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Status Service</h6>
        </div>
        <div class="card-body">
          <div class="row">

            <?php if (!empty($statusMap)): ?>
              <?php foreach ($statusMap as $key => $label): ?>
                <div class="col-xl-4 col-md-6 mb-3">
                  <div class="card shadow-sm h-100 py-2 border-left-info">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <?= esc($label) ?>
                          </div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= esc($statusCount[$key] ?? 0) ?>
                          </div>
                        </div>
                        <div class="col-auto">
                          <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12">
                <div class="text-muted">Status map belum tersedia.</div>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>

    <!-- BAGIAN 2: LOKASI -->
    <div class="col-lg-4 mb-4">
      <div class="card shadow">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Lokasi</h6>
        </div>
        <div class="card-body">

          <?php if (!empty($lokasiList)): ?>
            <?php foreach ($lokasiList as $nm): ?>
              <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div class="font-weight-bold text-gray-700"><?= esc($nm) ?></div>
                <div class="badge badge-primary p-2" style="min-width:50px; text-align:center;">
                  <?= esc($lokasiCount[strtoupper($nm)] ?? 0) ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-muted">Lokasi list belum tersedia.</div>
          <?php endif; ?>

          <small class="text-muted d-block mt-3">
            * Lokasi dihitung dari lokasi aset (aset.lokasiid) pada data service di tanggal terpilih.
          </small>

        </div>
      </div>
    </div>

  </div>

</div>

<?= $this->endSection() ?>