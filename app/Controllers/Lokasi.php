<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LokasiModel;

class Lokasi extends BaseController
{
    protected $lokasi;

    public function __construct()
    {
        $this->lokasi = new LokasiModel();
    }

    public function index()
    {
        $data['lokasi'] = $this->lokasi
            ->where('isdeleted', 0)
            ->orderBy('lokasiid', 'DESC')
            ->findAll();

        return view('lokasi/index', $data);
    }

    public function create()
    {
        return view('lokasi/form');
    }

    public function store()
    {
        $this->lokasi->insert([
            'lokasikode'  => $this->request->getPost('lokasikode'),
            'lokasi'      => $this->request->getPost('lokasi'),
            'isdeleted'   => 0,
            'createdby'   => 1,
            'createddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/lokasi');
    }

    public function edit($id)
    {
        $data['lokasi'] = $this->lokasi->find($id);
        return view('lokasi/form', $data);
    }

    public function update($id)
    {
        $this->lokasi->update($id, [
            'lokasikode'  => $this->request->getPost('lokasikode'),
            'lokasi'      => $this->request->getPost('lokasi'),
            'updatedby'   => 1,
            'updateddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/lokasi');
    }

    public function delete($id)
    {
        $this->lokasi->update($id, [
            'isdeleted'   => 1,
            'deletedby'   => 1,
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/lokasi');
    }
}