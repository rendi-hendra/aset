<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();

        // =========================
        // FILTER TANGGAL (GET)
        // =========================
        $date = (string) ($this->request->getGet('date') ?? '');
        $date = trim($date);

        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        // =========================
        // STATUS MAP (FIXED)
        // =========================
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

        // =========================
        // Subquery: detail TERAKHIR per asetservice (PostgreSQL DISTINCT ON)
        // =========================
        $latestServiceDtSql = '
            (
                SELECT DISTINCT ON (d.asetserviceid)
                    d.asetserviceid,
                    d.asetservicedate
                FROM asetservicedt d
                WHERE d.isdeleted = 0
                ORDER BY d.asetserviceid, d.asetservicedtid DESC
            ) dt
        ';

        // =========================
        // HITUNG STATUS (FILTER BY dt.asetservicedate)
        // =========================
        $rowsStatus = $db->table('asetservice s')
            ->select('s.servicestatusid, COUNT(*) as total')
            ->join($latestServiceDtSql, 'dt.asetserviceid = s.asetserviceid', 'inner', false)
            ->where('s.isdeleted', 0)
            ->where('dt.asetservicedate', $date)
            ->groupBy('s.servicestatusid')
            ->get()
            ->getResultArray();

        $statusCount = [];
        foreach ($statusMap as $key => $label) {
            $statusCount[$key] = 0;
        }
        foreach ($rowsStatus as $r) {
            $status = (int) $r['servicestatusid'];
            if (array_key_exists($status, $statusCount)) {
                $statusCount[$status] = (int) $r['total'];
            }
        }

        // =========================
        // LOKASI LIST (FIXED)
        // =========================
        $lokasiList = [
            'IGD',
            'POLI',
            'ADMISI',
            'FARMASI',
            'LAB',
            'RADIOLOGI',
        ];

        // =========================
        // OPSI B: LOKASI AMBIL DARI MUTASI TERAKHIR (asetmove)
        // - Kalau belum pernah mutasi, fallback ke aset.lokasiid
        // =========================
        $latestMoveSql = '
            (
                SELECT DISTINCT ON (am.asetid)
                    am.asetid,
                    am.lokasiakhirid
                FROM asetmove am
                WHERE am.isdeleted = 0
                ORDER BY am.asetid, am.createddate DESC, am.asetmoveid DESC
            ) lm
        ';

        $rowsLokasi = $db->table('asetservice s')
            ->select('UPPER(l.lokasi) AS lokasi, COUNT(*) AS total')
            ->join($latestServiceDtSql, 'dt.asetserviceid = s.asetserviceid', 'inner', false)
            ->join('aset a', 'a.asetid = s.asetid', 'left')
            ->join($latestMoveSql, 'lm.asetid = s.asetid', 'left', false)
            ->join('lokasi l', 'l.lokasiid = COALESCE(lm.lokasiakhirid, a.lokasiid)', 'left', false)
            ->where('s.isdeleted', 0)
            ->where('dt.asetservicedate', $date)
            ->groupBy('UPPER(l.lokasi)')
            ->get()
            ->getResultArray();

        $lokasiCount = [];
        foreach ($lokasiList as $nm) {
            $lokasiCount[$nm] = 0;
        }
        foreach ($rowsLokasi as $r) {
            $nm = strtoupper((string)($r['lokasi'] ?? ''));
            if ($nm !== '' && array_key_exists($nm, $lokasiCount)) {
                $lokasiCount[$nm] = (int) $r['total'];
            }
        }

        $data = [
            'totalUser'    => $userModel->where('isdeleted', 0)->countAllResults(),
            'selectedDate' => $date,

            'statusMap'    => $statusMap,
            'statusCount'  => $statusCount,

            'lokasiList'   => $lokasiList,
            'lokasiCount'  => $lokasiCount,
        ];

        return view('dashboard/index', $data);
    }
}