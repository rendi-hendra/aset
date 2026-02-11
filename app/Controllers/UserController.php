<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UserController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel
            ->where('isdeleted', 0)
            ->findAll();

        return view('users/index', $data);
    }

    public function create()
    {
        return view('users/form');
    }

    public function store()
    {
        $this->userModel->insert([
            'username'     => $this->request->getPost('username'),
            'nama'         => $this->request->getPost('nama'),
            'isdeleted'    => 0,
            'createdby'    => 1, // ganti dengan session login jika ada
            'createddate'  => date('Y-m-d H:i:s')
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
            'username'     => $this->request->getPost('username'),
            'nama'         => $this->request->getPost('nama'),
            'updatedby'    => 1,
            'updateddate'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/users');
    }

    public function delete($id)
    {
        $this->userModel->update($id, [
            'isdeleted'    => 1,
            'deletedby'    => 1,
            'deleteddate'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/users');
    }
}