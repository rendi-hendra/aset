<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetServiceModel extends Model
{
    protected $table            = 'asetservice';
    protected $primaryKey       = 'asetserviceid';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'asetid',
        'vendorid',
        'asetserviceno',
        'asetservicedate',
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

    public function getServiceList(): array
    {
        $db = \Config\Database::connect();

        return $db->table('asetservice s')
            ->select("
                s.asetserviceid,
                s.asetid,
                s.vendorid,
                s.asetserviceno,
                s.asetservicedate,
                s.remarks,
                s.servicestatusid,
                ss.servicestatus,
                s.isdeleted,
                s.createddate,
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
            ->join('"user" uc', 'uc.userid = s.createdby', 'left')
            ->join('"user" uu', 'uu.userid = s.updateby', 'left')
            ->join('"user" ud', 'ud.userid = s.deletedby', 'left')
            ->orderBy('s.asetserviceid', 'DESC')
            ->get()
            ->getResultArray();
    }
}