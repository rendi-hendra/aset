<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();

        // Mapping Status Service
        $statusMap = [
            0  => 'MASUK SERVICE',
            1  => 'PENGECEKAN',
            2  => 'PENGECEKAN LANJUT',
            3  => 'PERBAIKAN',
            4  => 'PERBAIKAN LANJUT',
            5  => 'SELESAI',
            98 => 'BATAL SERVICE',
            99 => 'BATAL RUSAK',
        ];

        // Ambil jumlah per servicestatusid dari tabel asetservice
        $rows = $db->table('asetservice')
            ->select('servicestatusid, COUNT(*) as total')
            ->where('isdeleted', 0)
            ->groupBy('servicestatusid')
            ->get()
            ->getResultArray();

        // Default semua status = 0
        $statusCount = [];
        foreach ($statusMap as $key => $label) {
            $statusCount[$key] = 0;
        }

        // Isi hasil query
        foreach ($rows as $r) {
            $status = (int)$r['servicestatusid'];
            $statusCount[$status] = (int)$r['total'];
        }

        $data = [
            'totalUser'   => $userModel->where('isdeleted', 0)->countAllResults(),
            'statusMap'   => $statusMap,
            'statusCount' => $statusCount,
        ];

        return view('dashboard/index', $data);
    }
}