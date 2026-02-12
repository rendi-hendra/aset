<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table      = 'lokasi';
    protected $primaryKey = 'lokasiid';

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
}