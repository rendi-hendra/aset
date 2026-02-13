<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-gray-800 m-0">USER</h4>

    <div class="d-flex" style="gap:10px;">
      <button type="button" class="btn btn-outline-secondary btn-sm" id="btnDefault">
        DEFAULT PAS BARU
      </button>

      <button type="submit" form="formUser" class="btn btn-primary btn-sm">
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
    <div class="col-lg-10 col-md-12">
      <div class="card shadow-sm">
        <div class="card-body">

          <form id="formUser" method="post" action="<?= base_url('users/save') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="userid" id="userid" value="">

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="mb-1">Username</label>
                  <input type="text" class="form-control" name="username" id="username" maxlength="20" required>
                </div>

                <div class="form-group">
                  <label class="mb-1">Password</label>
                  <input type="password" class="form-control" name="password" id="password" maxlength="200"
                    placeholder="Kosongkan = default / tidak berubah">
                  <small class="text-muted">* Saat edit: isi hanya jika ingin ganti password.</small>
                </div>

                <div class="form-group mb-0">
                  <label class="mb-1">Nama</label>
                  <input type="text" class="form-control" name="nama" id="nama" maxlength="100" required>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="mb-1">User Level</label>
                  <select class="form-control" name="userlevelid" id="userlevelid">
                    <option value="0">Superadmin</option>
                    <option value="1">Admin</option>
                    <option value="3" selected>User</option>
                    <option value="99">Guest</option>
                  </select>
                </div>

                <small class="text-muted d-block mt-2">
                  Klik baris di tabel untuk edit. Tombol <b>DEFAULT PAS BARU</b> untuk reset form & set level default.
                </small>
              </div>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered mb-0" id="tblUser">
        <thead class="bg-light">
          <tr>
            <th style="width:180px;">Username</th>
            <th>Nama</th>
            <th style="width:120px;">Status</th>
            <th style="width:180px;">Dibuat Oleh</th>
            <th style="width:180px;">Diubah Oleh</th>
            <th style="width:180px;">Dihapus Oleh</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada data.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <?php
              $statusText  = ((int)$u['isdeleted'] === 0) ? 'AKTIF' : 'TIDAK AKTIF';
              $createdDate = !empty($u['createddate']) ? date('Y-m-d', strtotime($u['createddate'])) : '';
              $dibuatOleh  = $u['createdby_name'] ?? '-';
              $diubahOleh  = $u['updatedby_name'] ?? '-';
              $dihapusOleh = $u['deletedby_name'] ?? '-';
              ?>
              <tr class="row-user"
                data-id="<?= (int)$u['userid'] ?>"
                data-username="<?= esc($u['username']) ?>"
                data-nama="<?= esc($u['nama']) ?>"
                data-userlevelid="<?= (int)($u['userlevelid'] ?? 3) ?>">
                <td><?= esc($u['username']) ?></td>
                <td><?= esc($u['nama']) ?></td>
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
  <form id="formDelete" method="post" action="<?= base_url('users/delete') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="userid" id="del_userid" value="">
  </form>

</div>

<style>
  #tblUser {
    table-layout: fixed;
    width: 100%;
  }

  #tblUser td,
  #tblUser th {
    vertical-align: middle;
  }

  #tblUser tbody tr {
    cursor: pointer;
  }

  #tblUser tbody tr:hover {
    background: #f8f9fc;
  }
</style>

<script>
  (function() {
    const btnDefault = document.getElementById('btnDefault');
    const btnHapus = document.getElementById('btnHapus');

    const userid = document.getElementById('userid');
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const nama = document.getElementById('nama');
    const userlevelid = document.getElementById('userlevelid');

    const del_userid = document.getElementById('del_userid');

    function resetFormDefault() {
      userid.value = '';
      username.value = '';
      password.value = '';
      nama.value = '';
      userlevelid.value = '2'; // default: User
      del_userid.value = '';
      btnHapus.disabled = true;

      document.querySelectorAll('#tblUser tbody tr').forEach(tr => tr.classList.remove('table-active'));
    }

    btnDefault.addEventListener('click', resetFormDefault);

    document.querySelectorAll('.row-user').forEach(tr => {
      tr.addEventListener('click', () => {
        document.querySelectorAll('#tblUser tbody tr').forEach(x => x.classList.remove('table-active'));
        tr.classList.add('table-active');

        userid.value = tr.dataset.id;
        username.value = tr.dataset.username;
        nama.value = tr.dataset.nama;
        userlevelid.value = tr.dataset.userlevelid || '2';

        // password jangan auto isi (demi keamanan)
        password.value = '';

        del_userid.value = tr.dataset.id;
        btnHapus.disabled = false;
      });
    });
  })();
</script>

<?= $this->endSection() ?>