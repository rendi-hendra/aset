<?php

namespace App\Controllers;

use App\Models\AsetMoveModel;

class MutasiAset extends BaseController
{
    protected $db;
    protected $moveModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->moveModel = new AsetMoveModel();
    }

    public function index()
    {
        // Filter GET
        $asetid = (int) ($this->request->getGet('asetid') ?? 0);
        $start  = trim((string) ($this->request->getGet('start') ?? ''));
        $end    = trim((string) ($this->request->getGet('end') ?? ''));

        // Dropdown Aset (ambil lokasiid terakhir juga)
        $asetList = $this->db->table('aset a')
            ->select('a.asetid, a.asetkode, j.jenis, m.merk, a.lokasiid')
            ->join('jenis j', 'j.jenisid = a.jenisid', 'left')
            ->join('merk m', 'm.merkid = a.merkid', 'left')
            ->where('a.isdeleted', 0)
            ->orderBy('a.asetkode', 'ASC')
            ->get()->getResultArray();

        // Dropdown Lokasi
        $lokasiList = $this->db->table('lokasi')
            ->select('lokasiid, lokasi')
            ->where('isdeleted', 0)
            ->orderBy('lokasi', 'ASC')
            ->get()->getResultArray();

        // Data mutasi
        $rows = $this->moveModel->getMutasiList(
            $asetid > 0 ? $asetid : null,
            $start !== '' ? $start : null,
            $end !== '' ? $end : null
        );

        return view('mutasi_aset/index', [
            'asetList'   => $asetList,
            'lokasiList' => $lokasiList,
            'rows'       => $rows,
            'filter'     => [
                'asetid' => $asetid,
                'start'  => $start,
                'end'    => $end,
            ],
        ]);
    }

    public function save()
    {
        $id            = trim((string)$this->request->getPost('asetmoveid'));
        $asetid        = (int)$this->request->getPost('asetid_form');
        $lokasiakhirid = (int)$this->request->getPost('lokasiakhirid');

        $userId = (int) session()->get('userid');

        if ($asetid <= 0 || $lokasiakhirid <= 0) {
            session()->setFlashdata('error', 'Lengkapi data: Aset dan Lokasi Akhir.');
            return redirect()->to(base_url('mutasi-aset'));
        }

        try {
            $this->db->transBegin();

            // Ambil lokasi terakhir aset -> ini sumber kebenaran untuk Lokasi Awal (khusus INSERT)
            $asetRow = $this->db->table('aset')
                ->select('asetid, lokasiid')
                ->where('asetid', $asetid)
                ->where('isdeleted', 0)
                ->get()->getRowArray();

            if (!$asetRow) {
                throw new \RuntimeException('Aset tidak ditemukan / tidak aktif.');
            }

            $lokasiawal_from_aset = (int)($asetRow['lokasiid'] ?? 0);
            if ($lokasiawal_from_aset <= 0) {
                throw new \RuntimeException('Aset ini belum punya lokasi terakhir (lokasiid kosong). Isi lokasi aset dulu.');
            }

            if ($id === '') {
                // INSERT: Lokasi Awal otomatis dari lokasi terakhir aset
                if ($lokasiawal_from_aset === $lokasiakhirid) {
                    throw new \RuntimeException('Lokasi Akhir tidak boleh sama dengan lokasi terakhir aset.');
                }

                $this->moveModel->insert([
                    'asetid'        => $asetid,
                    'asetmoveno'    => null, // biar trigger fn_gen_asetmoveno ngisi
                    'lokasiawalid'  => $lokasiawal_from_aset,
                    'lokasiakhirid' => $lokasiakhirid,
                    'isdeleted'     => 0,
                    'createdby'     => $userId,
                    'createddate'   => date('Y-m-d H:i:s'),
                ], false);

            } else {
                // UPDATE: jangan ubah lokasi awal (histori), dan idealnya aset juga jangan diganti
                $old = $this->moveModel->find((int)$id);
                if (!$old) {
                    throw new \RuntimeException('Data mutasi tidak ditemukan.');
                }

                if ((int)$old['asetid'] !== $asetid) {
                    throw new \RuntimeException('Aset pada data mutasi tidak boleh diganti.');
                }

                // Lokasi awal tetap pakai histori lama
                if ((int)$old['lokasiawalid'] === $lokasiakhirid) {
                    throw new \RuntimeException('Lokasi Akhir tidak boleh sama dengan Lokasi Awal.');
                }

                $this->moveModel->update((int)$id, [
                    'lokasiakhirid' => $lokasiakhirid,
                    'updatedby'     => $userId,
                    'updateddate'   => date('Y-m-d H:i:s'),
                ]);
            }

            // Update lokasi terakhir aset -> lokasi akhir terbaru
            $this->db->table('aset')
                ->where('asetid', $asetid)
                ->update([
                    'lokasiid'    => $lokasiakhirid,
                    'updatedby'   => $userId,
                    'updateddate' => date('Y-m-d H:i:s'),
                ]);

            $this->db->transCommit();
            session()->setFlashdata('success', 'Mutasi aset berhasil disimpan.');
            return redirect()->to(base_url('mutasi-aset'));

        } catch (\Throwable $e) {
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            session()->setFlashdata('error', 'Gagal simpan mutasi: ' . $e->getMessage());
            return redirect()->to(base_url('mutasi-aset'));
        }
    }

    public function delete()
    {
        $id = (int)$this->request->getPost('asetmoveid');
        $userId = (int) session()->get('userid');

        if ($id <= 0) {
            session()->setFlashdata('error', 'Pilih data mutasi dulu.');
            return redirect()->to(base_url('mutasi-aset'));
        }

        try {
            $this->moveModel->update($id, [
                'isdeleted'   => 1,
                'deletedby'   => $userId,
                'deleteddate' => date('Y-m-d H:i:s'),
            ]);

            session()->setFlashdata('success', 'Mutasi aset berhasil dihapus (soft delete).');
            return redirect()->to(base_url('mutasi-aset'));

        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Gagal hapus mutasi: ' . $e->getMessage());
            return redirect()->to(base_url('mutasi-aset'));
        }
    }
}