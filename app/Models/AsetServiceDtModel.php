<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetServiceDtModel extends Model
{
    protected $table            = 'asetservicedt';
    protected $primaryKey       = 'asetservicedtid';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'asetserviceid',
        'asetserviceno',
        'asetservicedate',
        'isdeleted',
        'createdby',
        'createddate',
        'updatedby',
        'updateddate',
        'deletedby',
        'deleteddate',
    ];

    public function getLatestByServiceId(int $asetserviceid): ?array
    {
        return $this->where('asetserviceid', $asetserviceid)
            ->where('isdeleted', 0)
            ->orderBy('asetservicedtid', 'DESC')
            ->first();
    }
}