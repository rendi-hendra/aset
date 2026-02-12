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

    /* =========================================
       LIST DATA
    ========================================= */
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('merk m');

        $builder->select('
            m.*,
            c.nama as createdby_name,
            u.nama as updatedby_name,
            d.nama as deletedby_name
        ');

        $builder->join('user c', 'c.userid = m.createdby', 'left');
        $builder->join('user u', 'u.userid = m.updatedby', 'left');
        $builder->join('user d', 'd.userid = m.deletedby', 'left');

        $builder->where('m.isdeleted', 0);
        $builder->orderBy('m.merkid', 'DESC');

        $data['merk'] = $builder->get()->getResultArray();

        return view('merk/index', $data);
    }

    /* =========================================
       FORM CREATE
    ========================================= */
    public function create()
    {
        return view('merk/form');
    }

    /* =========================================
       SIMPAN DATA
    ========================================= */
    public function store()
    {
        $this->merk->insert([
            'merk'        => $this->request->getPost('merk'),
            'isdeleted'   => 0,
            'createdby'   => session()->get('userid'),
            'createddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }

    /* =========================================
       FORM EDIT
    ========================================= */
    public function edit($id)
    {
        $data['merk'] = $this->merk->find($id);
        return view('merk/form', $data);
    }

    /* =========================================
       UPDATE DATA
    ========================================= */
    public function update($id)
    {
        $this->merk->update($id, [
            'merk'        => $this->request->getPost('merk'),
            'updatedby'   => session()->get('userid'),
            'updateddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }

    /* =========================================
       SOFT DELETE
    ========================================= */
    public function delete($id)
    {
        $this->merk->update($id, [
            'isdeleted'   => 1,
            'deletedby'   => session()->get('userid'),
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/merk');
    }
}