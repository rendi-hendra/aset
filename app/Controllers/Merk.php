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

        $data['merk'] = $merk;

        return view('merk/index', $data);
    }

    public function create()
    {
        return view('merk/form');
    }

    public function store()
    {
        $this->merkModel->insert([
            'merk'        => $this->request->getPost('merk'),
            'isdeleted'   => 0,
            'createdby'   => session()->get('userid'),
            'createddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }

    public function edit($id)
    {
        $data['merk'] = $this->merkModel->find($id);
        return view('merk/form', $data);
    }

    public function update($id)
    {
        $this->merkModel->update($id, [
            'merk'        => $this->request->getPost('merk'),
            'updatedby'   => session()->get('userid'),
            'updateddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }

    public function delete($id)
    {
        $this->merkModel->update($id, [
            'isdeleted'   => 1,
            'deletedby'   => session()->get('userid'),
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }
}
