<?php

namespace App\Controllers;

use App\Controllers\BaseController;
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

        return view('users/index', [
            'users' => $users,
        ]);
    }

    // Insert/Update + Restore (kalau username pernah dihapus)
    public function save()
    {
        $id          = $this->request->getPost('userid'); // bisa kosong
        $username    = strtolower(trim((string)$this->request->getPost('username')));
        $nama        = trim((string)$this->request->getPost('nama'));
        $userlevelid = (int)($this->request->getPost('userlevelid') ?? 2);
        $passwordIn  = (string)($this->request->getPost('password') ?? '');

        if ($username === '' || $nama === '') {
            return redirect()->to('/users')->with('error', 'Username dan Nama wajib diisi.');
        }

        // Validasi userlevel yang diizinkan
        $allowedLevels = [0, 1, 2, 99];
        if (!in_array($userlevelid, $allowedLevels, true)) {
            $userlevelid = 3;
        }

        $userIdLogin = (int) session()->get('userid');
        $now         = date('Y-m-d H:i:s');

        // Cari username yang sama (case-insensitive) termasuk yang isdeleted=1
        $existing = $this->userModel
            ->where('LOWER(username)', strtolower($username))
            ->first();

        // Helper ambil default password dari syssetting
        $getDefaultPassword = function (): string {
            $db = \Config\Database::connect();

            // cari berdasarkan variable dulu (lebih aman)
            $row = $db->table('syssetting')
                ->select('value')
                ->where('variable', 'DEFAULTUSERPASSWORD')
                ->get()
                ->getRowArray();

            if (!empty($row['value'])) return (string)$row['value'];

            // fallback: syssettingid=1 kalau kamu pakai patokan itu
            $row2 = $db->table('syssetting')
                ->select('value')
                ->where('syssettingid', 1)
                ->get()
                ->getRowArray();

            if (!empty($row2['value'])) return (string)$row2['value'];

            // fallback terakhir
            return 'Ekahusada-123';
        };

        if (!empty($existing)) {
            // Kalau sedang edit record yang sama -> lanjut update normal
            if (!empty($id) && (int)$existing['userid'] === (int)$id) {
                // lanjut
            } else {
                // Kalau ketemu tapi non-aktif -> RESTORE
                if ((int)$existing['isdeleted'] === 1) {
                    // password restore:
                    // - kalau user isi password, pakai itu
                    // - kalau kosong, biarkan password lama tetap (lebih aman)
                    $updateRestore = [
                        'username'    => $username,
                        'nama'        => $nama,
                        'userlevelid' => $userlevelid,
                        'isdeleted'   => 0,
                        'updatedby'   => $userIdLogin,
                        'updateddate' => $now,
                        'deletedby'   => null,
                        'deleteddate' => null,
                    ];

                    if (trim($passwordIn) !== '') {
                        $updateRestore['password'] = password_hash($passwordIn, PASSWORD_DEFAULT);
                    }

                    $this->userModel->update($existing['userid'], $updateRestore);

                    return redirect()->to('/users')->with('success', 'User sebelumnya non-aktif, sudah diaktifkan lagi.');
                }

                // Kalau masih aktif -> tolak
                return redirect()->to('/users')->with('error', 'Username sudah ada.');
            }
        }

        // UPDATE
        if (!empty($id)) {
            $payload = [
                'username'    => $username,
                'nama'        => $nama,
                'userlevelid' => $userlevelid,
                'updatedby'   => $userIdLogin,
                'updateddate' => $now,
            ];

            // Password hanya berubah kalau diisi
            if (trim($passwordIn) !== '') {
                $payload['password'] = password_hash($passwordIn, PASSWORD_DEFAULT);
            }

            $this->userModel->update($id, $payload);

            return redirect()->to('/users')->with('success', 'Data user berhasil diubah.');
        }

        // INSERT
        $passwordPlain = trim($passwordIn) !== '' ? $passwordIn : $getDefaultPassword();

        $this->userModel->insert([
            'username'    => $username,
            'nama'        => $nama,
            'userlevelid' => $userlevelid,
            'password'    => password_hash($passwordPlain, PASSWORD_DEFAULT),
            'isdeleted'   => 0,
            'createdby'   => $userIdLogin,
            'createddate' => $now,
        ]);

        return redirect()->to('/users')->with('success', 'Data user berhasil ditambahkan.');
    }

    // soft delete via POST
    public function delete()
    {
        $id = (int)($this->request->getPost('userid') ?? 0);

        if ($id <= 0) {
            return redirect()->to('/users')->with('error', 'Pilih user dulu.');
        }

        $this->userModel->update($id, [
            'isdeleted'   => 1,
            'deletedby'   => (int) session()->get('userid'),
            'deleteddate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/users')->with('success', 'User berhasil dihapus (non-aktif).');
    }
}
