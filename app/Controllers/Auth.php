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

        // Blokir guest
        if ($user['userlevelid'] == 99) {
            return redirect()->back()->with('error', 'Akun tidak memiliki akses sistem');
        }

        $session->set([
            'userid'       => $user['userid'],
            'username'     => $user['username'],
            'nama'         => $user['nama'],
            'userlevelid'  => $user['userlevelid'],
            'isLoggedIn'   => true
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}