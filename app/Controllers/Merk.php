<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MerkModel;

class Merk extends BaseController
{
    protected $merk;

    public function __construct()
    {
        $this->merk = new MerkModel();
    }

    public function index()
    {
        $data['merk'] = $this->merk
            ->where('isdeleted', 0)
            ->orderBy('merkid', 'DESC')
            ->findAll();

        return view('merk/index', $data);
    }

    public function create()
    {
        return view('merk/form');
    }

    public function store()
    {
        $this->merk->insert([
            'merk'        => $this->request->getPost('merk'),
            'isdeleted'   => 0,
            'createdby'   => 1,
            'createddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }

    public function edit($id)
    {
        $data['merk'] = $this->merk->find($id);
        return view('merk/form', $data);
    }

    public function update($id)
    {
        $this->merk->update($id, [
            'merk'        => $this->request->getPost('merk'),
            'updatedby'   => 1,
            'updateddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }

    public function delete($id)
    {
        $this->merk->update($id, [
            'isdeleted'   => 1,
            'deletedby'   => 1,
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }
}