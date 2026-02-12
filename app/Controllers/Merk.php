<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MerkModel;

class Merk extends BaseController
{
    protected $merkModel;

    public function __construct()
    {
        $this->merkModel = new MerkModel();
    }

    public function index()
    {
        $merk = $this->merkModel->getMerk()->findAll();

        return view('merk/index', [
            'merk' => $merk
        ]);
    }

    // 1 endpoint untuk insert/update + restore jika merk pernah dihapus
    public function save()
    {
        $id   = $this->request->getPost('merkid'); // bisa kosong saat insert
        $merk = trim((string)$this->request->getPost('merk'));

        if ($merk === '') {
            return redirect()->to('/merk')->with('error', 'Merk wajib diisi.');
        }

        $userId = (int) session()->get('userid');
        $now    = date('Y-m-d H:i:s');

        // Cari merk yang sama (case-insensitive), termasuk yang isdeleted=1
        $existing = $this->merkModel
            ->where('LOWER(merk)', strtolower($merk))
            ->first();

        if (!empty($existing)) {
            // Jika sedang edit record yang sama -> lanjut update normal
            if (!empty($id) && (int)$existing['merkid'] === (int)$id) {
                // lanjut ke update di bawah
            } else {
                // Jika merk ditemukan tapi non-aktif -> RESTORE (aktifkan lagi)
                if ((int)$existing['isdeleted'] === 1) {
                    $this->merkModel->update($existing['merkid'], [
                        'merk'        => $merk,
                        'isdeleted'   => 0,
                        'updatedby'   => $userId,
                        'updateddate' => $now,
                        'deletedby'   => null,
                        'deleteddate' => null,
                    ]);

                    return redirect()->to('/merk')->with('success', 'Merk sebelumnya non-aktif, sudah diaktifkan lagi.');
                }

                // Jika masih aktif -> tolak
                return redirect()->to('/merk')->with('error', 'Merk sudah ada.');
            }
        }

        // UPDATE (kalau ada id)
        if (!empty($id)) {
            $this->merkModel->update($id, [
                'merk'        => $merk,
                'updatedby'   => $userId,
                'updateddate' => $now,
            ]);

            return redirect()->to('/merk')->with('success', 'Data merk berhasil diubah.');
        }

        // INSERT (baru)
        $this->merkModel->insert([
            'merk'        => $merk,
            'isdeleted'   => 0,
            'createdby'   => $userId,
            'createddate' => $now,
        ]);

        return redirect()->to('/merk')->with('success', 'Data merk berhasil ditambahkan.');
    }

    // soft delete via POST
    public function delete()
    {
        $id = (int) ($this->request->getPost('merkid') ?? 0);

        if ($id <= 0) {
            return redirect()->to('/merk')->with('error', 'Pilih data merk dulu.');
        }

        $this->merkModel->update($id, [
            'isdeleted'   => 1,
            'deletedby'   => (int) session()->get('userid'),
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk')->with('success', 'Data merk berhasil dihapus (non-aktif).');
    }
}
