<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data User</h1>
    <a href="<?= base_url('users/create') ?>" class="btn btn-primary btn-sm shadow-sm">
      <i class="fas fa-plus fa-sm text-white-50"></i> Tambah User
    </a>
  </div>

  <div class="card shadow mb-4">

    <div class="card-body">
      <div class="table-responsive">
        <table id="tabelUser" class="table table-bordered" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th>Username</th>
              <th>Nama</th>
              <th>Created By</th>
              <th>Created Date</th>
              <th>Updated By</th>
              <th>Updated Date</th>
              <th width="15%" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?= esc($u['username']) ?></td>
                <td><?= esc($u['nama']) ?></td>
                <td><?= esc($u['createdby_name'] ?? '-') ?></td>
                <td><?= esc($u['createddate']) ?></td>
                <td><?= esc($u['updatedby_name'] ?? '-') ?></td>
                <td><?= esc($u['updateddate']) ?></td>
                <td class="text-center">
                  <a href="<?= base_url('users/edit/' . $u['userid']) ?>"
                    class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                  </a>

                  <a href="<?= base_url('users/delete/' . $u['userid']) ?>"
                    onclick="return confirm('Yakin hapus user ini?')"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<script src="<?= base_url('vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('js/demo/datatables-demo.js') ?>"></script>

<script>
  let table;

  $(function() {

    table = $('#tabelUser').DataTable({
      pageLength: 10,
      responsive: true,
      fixedHeader: true,
      order: [
        [1, 'asc']
      ],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
          previous: "‹",
          next: "›"
        },
        emptyTable: "Tidak ada data",
        zeroRecords: "Data tidak ditemukan"
      },
      columnDefs: [{
          targets: 0,
          orderable: false,
          searchable: false,
          className: 'text-center'
        },
        {
          targets: 3,
          orderable: false,
          searchable: false
        }
      ],
      dom: 'ltip'
    });

    // Nomor otomatis
    table.on('order.dt search.dt draw.dt', function() {
      table.column(0, {
          search: 'applied',
          order: 'applied'
        })
        .nodes()
        .each(function(cell, i) {
          cell.innerHTML = i + 1;
        });
    }).draw();

  });
</script>
<?= $this->endSection() ?>