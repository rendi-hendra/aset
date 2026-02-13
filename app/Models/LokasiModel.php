<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table      = 'lokasi';
    protected $primaryKey = 'lokasiid';

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'lokasikode',
        'lokasi',
        'isdeleted',
        'createdby',
        'createddate',
        'updatedby',
        'updateddate',
        'deletedby',
        'deleteddate'
    ];

    public function getLokasi()
    {
        // PostgreSQL: tabel "user" harus di-quote
        return $this->select('lokasi.*, c.nama as createdby_name, up.nama as updatedby_name, d.nama as deletedby_name')
            ->join('"user" c', 'c.userid = lokasi.createdby', 'left')
            ->join('"user" up', 'up.userid = lokasi.updatedby', 'left')
            ->join('"user" d', 'd.userid = lokasi.deletedby', 'left')
            ->orderBy('lokasi.createddate', 'DESC');
    }
}
