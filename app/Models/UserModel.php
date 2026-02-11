<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'user';
    protected $primaryKey = 'userid';

    protected $allowedFields = [
        'username',
        'nama',
        'isdeleted',
        'createdby',
        'createddate',
        'updatedby',
        'updateddate',
        'deletedby',
        'deleteddate'
    ];

    protected $useTimestamps = false;
}