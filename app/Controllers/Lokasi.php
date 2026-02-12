<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LokasiModel;

class Lokasi extends BaseController
{
    protected $lokasiModel;

    public function __construct()
    {
        $this->lokasiModel = new LokasiModel();
    }

    public function index()
    {
        $lokasi = $this->lokasiModel->getLokasi()->findAll();

        return view('lokasi/index', [
            'lokasi' => $lokasi
        ]);
    }

    // 1 endpoint untuk insert/update + restore jika lokasikode pernah dihapus
    public function save()
    {
        $id = $this->request->getPost('lokasiid'); // bisa kosong saat insert

        $lokasikode = strtoupper(trim((string)$this->request->getPost('lokasikode')));
        $lokasi     = trim((string)$this->request->getPost('lokasi'));

        if ($lokasikode === '' || $lokasi === '') {
            return redirect()->to('/lokasi')->with('error', 'Kode dan Lokasi wajib diisi.');
        }

        $userId = (int) session()->get('userid');
        $now    = date('Y-m-d H:i:s');

        // Cari lokasikode yang sama, termasuk yang isdeleted=1
        $existing = $this->lokasiModel
            ->where('LOWER(lokasikode)', strtolower($lokasikode))
            ->first();

        if (!empty($existing)) {
            // Jika sedang edit record yang sama -> lanjut update normal
            if (!empty($id) && (int)$existing['lokasiid'] === (int)$id) {
                // lanjut
            } else {
                // Kalau ketemu tapi non-aktif -> RESTORE
                if ((int)$existing['isdeleted'] === 1) {
                    $this->lokasiModel->update($existing['lokasiid'], [
                        'lokasikode'  => $lokasikode,
                        'lokasi'      => $lokasi,
                        'isdeleted'   => 0,
                        'updatedby'   => $userId,
                        'updateddate' => $now,
                        'deletedby'   => null,
                        'deleteddate' => null,
                    ]);

                    return redirect()->to('/lokasi')->with('success', 'Lokasi sebelumnya non-aktif, sudah diaktifkan lagi.');
                }

                // Kalau masih aktif -> tolak
                return redirect()->to('/lokasi')->with('error', 'Kode lokasi sudah digunakan.');
            }
        }

        // UPDATE
        if (!empty($id)) {
            $this->lokasiModel->update($id, [
                'lokasikode'  => $lokasikode,
                'lokasi'      => $lokasi,
                'updatedby'   => $userId,
                'updateddate' => $now,
            ]);

            return redirect()->to('/lokasi')->with('success', 'Data lokasi berhasil diubah.');
        }

        // INSERT
        $this->lokasiModel->insert([
            'lokasikode'  => $lokasikode,
            'lokasi'      => $lokasi,
            'isdeleted'   => 0,
            'createdby'   => $userId,
            'createddate' => $now,
        ]);

        return redirect()->to('/lokasi')->with('success', 'Data lokasi berhasil ditambahkan.');
    }

    // soft delete via POST
    public function delete()
    {
        $id = (int) ($this->request->getPost('lokasiid') ?? 0);

        if ($id <= 0) {
            return redirect()->to('/lokasi')->with('error', 'Pilih data lokasi dulu.');
        }

        $this->lokasiModel->update($id, [
            'isdeleted'   => 1,
            'deletedby'   => (int) session()->get('userid'),
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/lokasi')->with('success', 'Data lokasi berhasil dihapus (non-aktif).');
    }
}
