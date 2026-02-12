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

        $data['lokasi'] = $lokasi;

        return view('lokasi/index', $data);
    }

    public function create()
    {
        return view('lokasi/form');
    }

    public function store()
    {
        $this->lokasiModel->insert([
            'lokasikode'  => $this->request->getPost('lokasikode'),
            'lokasi'      => $this->request->getPost('lokasi'),
            'isdeleted'   => 0,
            'createdby'   => session()->get('userid'),
            'createddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/lokasi');
    }

    public function edit($id)
    {
        $data['lokasi'] = $this->lokasiModel->find($id);
        return view('lokasi/form', $data);
    }

    public function update($id)
    {
        $this->lokasiModel->update($id, [
            'lokasikode'  => $this->request->getPost('lokasikode'),
            'lokasi'      => $this->request->getPost('lokasi'),
            'updatedby'   => session()->get('userid'),
            'updateddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/lokasi');
    }

    public function delete($id)
    {
        $this->lokasiModel->update($id, [
            'isdeleted'   => 1,
            'deletedby'   => session()->get('userid'),
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/lokasi');
    }
}
