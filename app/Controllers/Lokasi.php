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

    // 1 endpoint untuk insert/update
    public function save()
    {
        $id = $this->request->getPost('lokasiid');

        $lokasikode = strtoupper(trim((string)$this->request->getPost('lokasikode')));
        $lokasi     = trim((string)$this->request->getPost('lokasi'));

        if ($lokasikode === '' || $lokasi === '') {
            return redirect()->to('/lokasi')->with('error', 'Kode dan Lokasi wajib diisi.');
        }

        // Validasi unik lokasikode (biar tidak dobel)
        $exist = $this->lokasiModel->where('lokasikode', $lokasikode);
        if (!empty($id)) {
            $exist->where('lokasiid !=', $id);
        }
        if ($exist->countAllResults() > 0) {
            return redirect()->to('/lokasi')->with('error', 'Kode lokasi sudah digunakan.');
        }

        $userId = (int) session()->get('userid');
        $now    = date('Y-m-d H:i:s');

        if (!empty($id)) {
            // UPDATE
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

    // soft delete via POST (lebih aman)
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
