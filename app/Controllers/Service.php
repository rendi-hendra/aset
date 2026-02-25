<?php

namespace App\Controllers;

use App\Models\AsetServiceModel;
use App\Models\AsetServiceDtModel;

class Service extends BaseController
{
    protected $db;
    protected $serviceModel;
    protected $serviceDtModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->serviceModel = new AsetServiceModel();
        $this->serviceDtModel = new AsetServiceDtModel();
    }

    public function index()
    {
        $asetList = $this->db->table('aset a')
            ->select('a.asetid, a.asetkode, j.jenis, m.merk')
            ->join('jenis j', 'j.jenisid = a.jenisid', 'left')
            ->join('merk m', 'm.merkid = a.merkid', 'left')
            ->where('a.isdeleted', 0)
            ->orderBy('a.asetkode', 'ASC')
            ->get()->getResultArray();

        $vendorList = $this->db->table('vendor')
            ->select('vendorid, vendor')
            ->where('isdeleted', 0)
            ->orderBy('vendor', 'ASC')
            ->get()->getResultArray();

        $statusList = $this->db->table('servicestatus')
            ->select('servicestatusid, servicestatus')
            ->orderBy('servicestatusid', 'ASC')
            ->get()->getResultArray();

        $service = $this->serviceModel->getServiceList();

        return view('service/index', [
            'asetList'   => $asetList,
            'vendorList' => $vendorList,
            'statusList' => $statusList,
            'service'    => $service,
        ]);
    }

    public function save()
    {
        $id              = trim((string) $this->request->getPost('asetserviceid'));
        $asetid          = (int) $this->request->getPost('asetid');
        $vendorid        = (int) $this->request->getPost('vendorid');
        $tglService      = trim((string) $this->request->getPost('asetservicedate'));
        $noService       = trim((string) $this->request->getPost('asetserviceno'));
        $statusServiceId = (int) $this->request->getPost('servicestatusid');
        $remarks         = trim((string) $this->request->getPost('remarks'));

        $userId = (int) session()->get('userid');
        $now    = date('Y-m-d H:i:s');

        if ($asetid <= 0 || $vendorid <= 0 || $tglService === '' || $noService === '' || $statusServiceId < 0 || $remarks === '') {
            session()->setFlashdata('error', 'Lengkapi data: Aset, Vendor, Tgl Service, No Service, Status Service, Keterangan.');
            return redirect()->to(base_url('service'));
        }

        try {
            $this->db->transBegin();

            if ($id === '') {
                // INSERT HEADER
                $this->serviceModel->insert([
                    'asetid'         => $asetid,
                    'vendorid'       => $vendorid,
                    'remarks'        => $remarks,
                    'servicestatusid'=> $statusServiceId,
                    'isdeleted'      => 0,
                    'createdby'      => $userId,
                    'createddate'    => $now,
                ], false);

                $newServiceId = (int) $this->db->insertID();

                // INSERT DETAIL
                $this->serviceDtModel->insert([
                    'asetserviceid'  => $newServiceId,
                    'asetserviceno'  => $noService,
                    'asetservicedate'=> $tglService,
                    'isdeleted'      => 0,
                    'createdby'      => $userId,
                    'createddate'    => $now,
                ], false);

                $this->db->transCommit();
                session()->setFlashdata('success', 'Data service berhasil ditambahkan.');
                return redirect()->to(base_url('service'));
            }

            // UPDATE HEADER
            $serviceId = (int) $id;

            $this->serviceModel->update($serviceId, [
                'asetid'          => $asetid,
                'vendorid'        => $vendorid,
                'remarks'         => $remarks,
                'servicestatusid' => $statusServiceId,
                'updateby'        => $userId, // kolomnya updateby
                'updateddate'     => $now,
            ]);

            // UPDATE DETAIL TERAKHIR (atau insert kalau belum ada)
            $latestDt = $this->serviceDtModel->getLatestByServiceId($serviceId);

            if ($latestDt) {
                $this->serviceDtModel->update((int) $latestDt['asetservicedtid'], [
                    'asetserviceno'   => $noService,
                    'asetservicedate' => $tglService,
                    'updatedby'       => $userId,
                    'updateddate'     => $now,
                ]);
            } else {
                $this->serviceDtModel->insert([
                    'asetserviceid'   => $serviceId,
                    'asetserviceno'   => $noService,
                    'asetservicedate' => $tglService,
                    'isdeleted'       => 0,
                    'createdby'       => $userId,
                    'createddate'     => $now,
                ], false);
            }

            $this->db->transCommit();
            session()->setFlashdata('success', 'Data service berhasil diupdate.');
            return redirect()->to(base_url('service'));

        } catch (\Throwable $e) {
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            session()->setFlashdata('error', 'Gagal simpan service: ' . $e->getMessage());
            return redirect()->to(base_url('service'));
        }
    }

    public function delete()
    {
        $id     = (int) $this->request->getPost('asetserviceid');
        $userId = (int) session()->get('userid');
        $now    = date('Y-m-d H:i:s');

        if ($id <= 0) {
            session()->setFlashdata('error', 'Pilih data service dulu.');
            return redirect()->to(base_url('service'));
        }

        try {
            $this->db->transBegin();

            // soft delete header
            $this->serviceModel->update($id, [
                'isdeleted'   => 1,
                'deletedby'   => $userId,
                'deleteddate' => $now,
            ]);

            // soft delete semua detail
            $this->db->table('asetservicedt')
                ->where('asetserviceid', $id)
                ->where('isdeleted', 0)
                ->update([
                    'isdeleted'   => 1,
                    'deletedby'   => $userId,
                    'deleteddate' => $now,
                ]);

            $this->db->transCommit();

            session()->setFlashdata('success', 'Data service berhasil dihapus (soft delete).');
            return redirect()->to(base_url('service'));
        } catch (\Throwable $e) {
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            session()->setFlashdata('error', 'Gagal hapus service: ' . $e->getMessage());
            return redirect()->to(base_url('service'));
        }
    }
}