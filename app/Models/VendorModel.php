<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table            = 'vendor';
    protected $primaryKey       = 'vendorid';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'vendor',
        'alamat',
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
                vendor.*,
                u1.nama as createdby_name,
                u2.nama as updatedby_name,
                u3.nama as deletedby_name
            ")
            ->join('"user" u1', 'u1.userid = vendor.createdby', 'left')
            ->join('"user" u2', 'u2.userid = vendor.updatedby', 'left')
            ->join('"user" u3', 'u3.userid = vendor.deletedby', 'left')
            ->orderBy('vendor.createddate', 'ASC');
    }
}