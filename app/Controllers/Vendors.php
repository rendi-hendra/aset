<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VendorModel;

class Vendors extends BaseController
{
    protected $vendorModel;

    public function __construct()
    {
        $this->vendorModel = new VendorModel();
    }

    public function index()
    {
        $data['vendor'] = $this->vendorModel
                                ->getAll()
                                ->findAll();

        return view('vendors/index', $data);
    }

    public function save()
    {
        $vendorid = $this->request->getPost('vendorid');
        $vendor   = trim($this->request->getPost('vendor'));
        $alamat   = trim($this->request->getPost('alamat'));

        if ($vendor === '') {
            return redirect()->back()->with('error', 'Nama vendor wajib diisi.');
        }

        $userId = session()->get('userid') ?? 1;
        $now    = date('Y-m-d H:i:s');

        if (empty($vendorid)) {

            $this->vendorModel->insert([
                'vendor'      => $vendor,
                'alamat'      => $alamat,
                'isdeleted'   => 0,
                'createdby'   => $userId,
                'createddate' => $now
            ]);

            return redirect()->to('/vendors')->with('success', 'Vendor berhasil ditambahkan.');
        } else {

            $this->vendorModel->update($vendorid, [
                'vendor'      => $vendor,
                'alamat'      => $alamat,
                'updatedby'   => $userId,
                'updateddate' => $now
            ]);

            return redirect()->to('/vendors')->with('success', 'Vendor berhasil diperbarui.');
        }
    }

    public function delete()
    {
        $vendorid = $this->request->getPost('vendorid');

        if (empty($vendorid)) {
            return redirect()->to('/vendors')->with('error', 'Data tidak valid.');
        }

        $userId = session()->get('userid') ?? 1;
        $now    = date('Y-m-d H:i:s');

        $this->vendorModel->update($vendorid, [
            'isdeleted'   => 1,
            'deletedby'   => $userId,
            'deleteddate' => $now
        ]);

        return redirect()->to('/vendors')->with('success', 'Vendor berhasil dihapus.');
    }
}