<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetServiceModel extends Model
{
    protected $table            = 'asetservice';
    protected $primaryKey       = 'asetserviceid';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    // asetserviceno + asetservicedate SUDAH DIPINDAH ke asetservicedt
    protected $allowedFields = [
        'asetid',
        'vendorid',
        'remarks',
        'servicestatusid',
        'isdeleted',
        'createdby',
        'createddate',
        'updateby',
        'updateddate',
        'deletedby',
        'deleteddate',
    ];

    /**
     * List service: ambil nomor+tanggal dari detail TERAKHIR (asetservicedt)
     */
    public function getServiceList(): array
    {
        // Subquery PostgreSQL: ambil detail terakhir per asetserviceid
        $latestDtSql = '
            (
                SELECT DISTINCT ON (d.asetserviceid)
                    d.asetserviceid,
                    d.asetservicedtid,
                    d.asetserviceno,
                    d.asetservicedate
                FROM asetservicedt d
                WHERE d.isdeleted = 0
                ORDER BY d.asetserviceid, d.asetservicedtid DESC
            ) dt
        ';

        return $this->db->table('asetservice s')
            ->select("
                s.asetserviceid,
                s.asetid,
                s.vendorid,
                dt.asetserviceno,
                dt.asetservicedate,
                s.remarks,
                s.servicestatusid,
                ss.servicestatus,
                s.isdeleted,
                s.createddate,
                s.updateddate,
                s.deleteddate,
                uc.nama as createdby_name,
                uu.nama as updatedby_name,
                ud.nama as deletedby_name,
                a.asetkode,
                j.jenis,
                m.merk,
                v.vendor
            ")
            ->join('aset a', 'a.asetid = s.asetid', 'left')
            ->join('jenis j', 'j.jenisid = a.jenisid', 'left')
            ->join('merk m', 'm.merkid = a.merkid', 'left')
            ->join('vendor v', 'v.vendorid = s.vendorid', 'left')
            ->join('servicestatus ss', 'ss.servicestatusid = s.servicestatusid', 'left')
            ->join($latestDtSql, 'dt.asetserviceid = s.asetserviceid', 'left', false)
            ->join('"user" uc', 'uc.userid = s.createdby', 'left')
            ->join('"user" uu', 'uu.userid = s.updateby', 'left')
            ->join('"user" ud', 'ud.userid = s.deletedby', 'left')
            ->orderBy('s.asetserviceid', 'DESC')
            ->get()
            ->getResultArray();
    }
}