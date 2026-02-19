<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetMoveModel extends Model
{
    protected $table            = 'asetmove';
    protected $primaryKey       = 'asetmoveid';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'asetid',
        'asetmoveno',
        'lokasiawalid',
        'lokasiakhirid',
        'isdeleted',
        'createdby',
        'createddate',
        'updatedby',
        'updateddate',
        'deletedby',
        'deleteddate',
        'lokasiawal',
        'lokasiakhir',
    ];

    public function getMutasiList(?int $asetid, ?string $startDate, ?string $endDate): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('asetmove am')
            ->select("
                am.asetmoveid,
                am.asetmoveno,
                am.asetid,
                am.createddate,
                am.lokasiawalid,
                am.lokasiakhirid,
                am.isdeleted,

                a.asetkode,
                a.pembelianno,
                j.jenis,
                m.merk,

                la.lokasi as lokasi_awal,
                lk.lokasi as lokasi_akhir,

                uc.nama as createdby_name,
                uu.nama as updatedby_name,
                ud.nama as deletedby_name
            ")
            ->join('aset a', 'a.asetid = am.asetid', 'left')
            ->join('jenis j', 'j.jenisid = a.jenisid', 'left')
            ->join('merk m', 'm.merkid = a.merkid', 'left')
            ->join('lokasi la', 'la.lokasiid = am.lokasiawalid', 'left')
            ->join('lokasi lk', 'lk.lokasiid = am.lokasiakhirid', 'left')
            ->join('"user" uc', 'uc.userid = am.createdby', 'left')
            ->join('"user" uu', 'uu.userid = am.updatedby', 'left')
            ->join('"user" ud', 'ud.userid = am.deletedby', 'left')
            ->orderBy('am.asetmoveid', 'DESC');

        if (!empty($asetid) && $asetid > 0) {
            $builder->where('am.asetid', $asetid);
        }

        if (!empty($startDate)) {
            $builder->where("DATE(am.createddate) >=", $startDate);
        }

        if (!empty($endDate)) {
            $builder->where("DATE(am.createddate) <=", $endDate);
        }

        return $builder->get()->getResultArray();
    }
}