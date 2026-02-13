<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisModel extends Model
{
    protected $table            = 'jenis';   // sesuai tabel Anda
    protected $primaryKey       = 'jenisid';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'jeniskode',
        'jenis',
        'isdeleted',
        'createdby',
        'createddate',
        'updatedby',
        'updateddate',
        'deletedby',
        'deleteddate'
    ];

    protected $useTimestamps = false;

    public function getAll()
    {
        return $this->select("
                jenis.*,
                u1.nama as createdby_name,
                u2.nama as updatedby_name,
                u3.nama as deletedby_name
            ")
            ->join('user u1', 'u1.userid = jenis.createdby', 'left')
            ->join('user u2', 'u2.userid = jenis.updatedby', 'left')
            ->join('user u3', 'u3.userid = jenis.deletedby', 'left')
            ->orderBy('jeniskode', 'ASC')
            ->findAll();
    }
}