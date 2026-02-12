<?php

namespace App\Models;

use CodeIgniter\Model;

class MerkModel extends Model
{
    protected $table      = 'merk';
    protected $primaryKey = 'merkid';

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'merk',
        'isdeleted',
        'createdby',
        'createddate',
        'updatedby',
        'updateddate',
        'deletedby',
        'deleteddate'
    ];

    public function getMerk()
    {
        // PostgreSQL: tabel "user" harus di-quote
        return $this->select('merk.*, c.nama as createdby_name, up.nama as updatedby_name, d.nama as deletedby_name')
            ->join('"user" c', 'c.userid = merk.createdby', 'left')
            ->join('"user" up', 'up.userid = merk.updatedby', 'left')
            ->join('"user" d', 'd.userid = merk.deletedby', 'left')
            ->where('merk.isdeleted', 0)
            ->orderBy('merk.createddate', 'DESC');
    }
}
