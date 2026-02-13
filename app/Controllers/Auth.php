<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function processLogin()
    {
        $session = session();
        $model   = new UserModel();
        var_dump($model->first());

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model
            ->where('username', $username)
            ->where('isdeleted', 0)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Username tidak ditemukan');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Username atau password salah');
        }

        $sessionData = [
            'userid'     => $user['userid'],
            'username'   => $user['username'],
            'nama'       => $user['nama'],
            'isLoggedIn' => true
        ];

        $session->set($sessionData);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
