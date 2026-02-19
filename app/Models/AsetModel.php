<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetModel extends Model
{
    protected $table            = 'aset';
    protected $primaryKey       = 'asetid';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'jenisid',
        'merkid',
        'lokasiid',
        'asetkode',
        'pembeliandate',
        'pembelianno',
        'isdeleted',
        'createdby',
        'createddate',
        'updatedby',
        'updateddate',
        'deletedby',
        'deleteddate',
    ];

    public function getAsetList(): array
    {
        $db = \Config\Database::connect();

        // Join untuk tampil jenis/merk/lokasi + nama user created/updated/deleted
        return $db->table('aset a')
            ->select("
                a.asetid,
                a.asetkode,
                a.jenisid,
                j.jenis as jenis,
                a.merkid,
                m.merk as merk,
                a.lokasiid,
                l.lokasi as lokasi,
                a.pembeliandate,
                a.pembelianno,
                a.isdeleted,
                a.createddate,
                uc.nama as createdby_name,
                uu.nama as updatedby_name,
                ud.nama as deletedby_name
            ")
            ->join('jenis j', 'j.jenisid = a.jenisid', 'left')
            ->join('merk m', 'm.merkid = a.merkid', 'left')
            ->join('lokasi l', 'l.lokasiid = a.lokasiid', 'left')
            ->join('"user" uc', 'uc.userid = a.createdby', 'left')
            ->join('"user" uu', 'uu.userid = a.updatedby', 'left')
            ->join('"user" ud', 'ud.userid = a.deletedby', 'left')
            ->orderBy('a.asetid', 'DESC')
            ->get()
            ->getResultArray();
    }
}