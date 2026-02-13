<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JenisModel;

class Jenis extends BaseController
{
    protected $jenisModel;

    public function __construct()
    {
        $this->jenisModel = new JenisModel();
    }

    public function index()
    {
        $data['jenis'] = $this->jenisModel->getAll();
        return view('jenis/index', $data);
    }

    public function save()
    {
        $jenisid   = $this->request->getPost('jenisid');
        $jeniskode = strtoupper(trim($this->request->getPost('kode')));
        $jenis     = trim($this->request->getPost('jenis_aset'));

        if ($jeniskode === '' || $jenis === '') {
            return redirect()->back()->with('error', 'Kode dan Jenis wajib diisi.');
        }

        $userId = session()->get('userid') ?? 1;
        $now    = date('Y-m-d H:i:s');

        if (empty($jenisid)) {

            $this->jenisModel->insert([
                'jeniskode'  => $jeniskode,
                'jenis'      => $jenis,
                'isdeleted'  => 0,
                'createddate'=> $now,
                'createdby'  => $userId
            ]);

            return redirect()->to('/jenis')->with('success', 'Data berhasil ditambahkan.');
        } else {

            $this->jenisModel->update($jenisid, [
                'jeniskode'  => $jeniskode,
                'jenis'      => $jenis,
                'updateddate'=> $now,
                'updatedby'  => $userId
            ]);

            return redirect()->to('/jenis')->with('success', 'Data berhasil diperbarui.');
        }
    }

    public function delete()
    {
        $jenisid = $this->request->getPost('jenisid');

        if (empty($jenisid)) {
            return redirect()->to('/jenis')->with('error', 'Data tidak valid.');
        }

        $userId = session()->get('userid') ?? 1;
        $now    = date('Y-m-d H:i:s');

        $this->jenisModel->update($jenisid, [
            'isdeleted'  => 1,
            'deleteddate'=> $now,
            'deletedby'  => $userId
        ]);

        return redirect()->to('/jenis')->with('success', 'Data berhasil dihapus.');
    }
}