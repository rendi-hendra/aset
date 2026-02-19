<?php

namespace App\Controllers;

use App\Models\AsetModel;

class Aset extends BaseController
{
    protected $db;
    protected $asetModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->asetModel = new AsetModel();
    }

    public function index()
    {
        $jenis = $this->db->table('jenis')
            ->select('jenisid, jenis')
            ->where('isdeleted', 0)
            ->orderBy('jenis', 'ASC')
            ->get()->getResultArray();

        $merk = $this->db->table('merk')
            ->select('merkid, merk')
            ->where('isdeleted', 0)
            ->orderBy('merk', 'ASC')
            ->get()->getResultArray();

        $lokasi = $this->db->table('lokasi')
            ->select('lokasiid, lokasi')
            ->where('isdeleted', 0)
            ->orderBy('lokasi', 'ASC')
            ->get()->getResultArray();

        $aset = $this->asetModel->getAsetList();

        return view('aset/index', [
            'jenis'  => $jenis,
            'merk'   => $merk,
            'lokasi' => $lokasi,
            'aset'   => $aset,
        ]);
    }

    public function save()
    {
        $asetid        = trim((string) $this->request->getPost('asetid'));
        $asetkodeInput = trim((string) $this->request->getPost('asetkode'));
        $jenisid       = (int) $this->request->getPost('jenisid');
        $merkid        = (int) $this->request->getPost('merkid');
        $lokasiid      = (int) $this->request->getPost('lokasiid');
        $pembeliandate = trim((string) $this->request->getPost('pembeliandate'));
        $pembelianno   = trim((string) $this->request->getPost('pembelianno'));

        // asumsi kamu simpan userid di session seperti sebelumnya
        $userId = (int) session()->get('userid');

        // Validasi minimal sesuai DB
        if ($jenisid <= 0 || $merkid <= 0 || $lokasiid <= 0 || $pembeliandate === '' || $pembelianno === '') {
            session()->setFlashdata('error', 'Lengkapi data: Jenis, Merk, Lokasi, Tgl Pembelian, No Pembelian.');
            return redirect()->to(base_url('aset'));
        }

        // asetkode: kalau kosong => NULL supaya trigger DB generate otomatis
        $asetkode = ($asetkodeInput === '') ? null : $asetkodeInput;

        $dataBase = [
            'jenisid'       => $jenisid,
            'merkid'        => $merkid,
            'lokasiid'      => $lokasiid,
            'asetkode'      => $asetkode,
            'pembeliandate' => $pembeliandate,
            'pembelianno'   => $pembelianno,
        ];

        try {
            $this->db->transBegin();

            if ($asetid === '') {
                // INSERT
                $dataInsert = array_merge($dataBase, [
                    'isdeleted'   => 0,
                    'createdby'   => $userId,
                    'createddate' => date('Y-m-d H:i:s'),
                ]);

                $this->asetModel->insert($dataInsert, false);
                $this->db->transCommit();

                session()->setFlashdata('success', 'Aset berhasil ditambahkan.');
                return redirect()->to(base_url('aset'));
            }

            // UPDATE
            $id = (int) $asetid;

            $dataUpdate = array_merge($dataBase, [
                'updatedby'   => $userId,
                'updateddate' => date('Y-m-d H:i:s'),
            ]);

            $this->asetModel->update($id, $dataUpdate);
            $this->db->transCommit();

            session()->setFlashdata('success', 'Aset berhasil diupdate.');
            return redirect()->to(base_url('aset'));
        } catch (\Throwable $e) {
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            session()->setFlashdata('error', 'Gagal simpan aset: ' . $e->getMessage());
            return redirect()->to(base_url('aset'));
        }
    }

    public function delete()
    {
        $asetid = (int) $this->request->getPost('asetid');
        $userId = (int) session()->get('userid');

        if ($asetid <= 0) {
            session()->setFlashdata('error', 'Pilih data aset dulu.');
            return redirect()->to(base_url('aset'));
        }

        try {
            $this->asetModel->update($asetid, [
                'isdeleted'   => 1,
                'deletedby'   => $userId,
                'deleteddate' => date('Y-m-d H:i:s'),
            ]);

            session()->setFlashdata('success', 'Aset berhasil dihapus (soft delete).');
            return redirect()->to(base_url('aset'));
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Gagal hapus aset: ' . $e->getMessage());
            return redirect()->to(base_url('aset'));
        }
    }
}