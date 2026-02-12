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
        $users = $this->userModel->getUser()->findAll();

        $data['users'] = $users;

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
            'isdeleted' => 1,
            'deletedby'   => session()->get('userid'),
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/users');
    }
}
