<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
{
    $db = \Config\Database::connect();
    $builder = $db->table('user u');

    $builder->select('
        u.*,
        c.nama as createdby_name,
        up.nama as updatedby_name,
        d.nama as deletedby_name
    ');

    $builder->join('user c', 'c.userid = u.createdby', 'left');
    $builder->join('user up', 'up.userid = u.updatedby', 'left');
    $builder->join('user d', 'd.userid = u.deletedby', 'left');

    $builder->where('u.deleteddate IS NULL');

    $data['users'] = $builder->get()->getResultArray();

    return view('users/index', $data);
}

    public function create()
    {
        return view('users/form');
    }

    public function store()
    {
        $this->userModel->insert([
            'username'    => $this->request->getPost('username'),
            'nama'        => $this->request->getPost('nama'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'createdby'   => session()->get('userid'),
            'createddate' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/users');
    }

    public function edit($id)
    {
        $data['user'] = $this->userModel->find($id);
        return view('users/form', $data);
    }

    public function update($id)
    {
        $this->userModel->update($id, [
            'username'    => $this->request->getPost('username'),
            'nama'        => $this->request->getPost('nama'),
            'updatedby'   => session()->get('userid'),
            'updateddate' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/users');
    }

    public function delete($id)
    {
        $this->userModel->update($id, [
            'deletedby'   => session()->get('userid'),
            'deleteddate' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/users');
    }
}