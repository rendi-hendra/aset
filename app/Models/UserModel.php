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
        'password',
        'isdeleted',
        'createdby',
        'createddate',
        'updatedby',
        'updateddate',
        'deletedby',
        'deleteddate'
    ];

    protected $useTimestamps = false;

    public function getUser()
    {
        return $this->select('user.*,c.nama as createdby_name,up.nama as updatedby_name,d.nama as deletedby_name')
            ->join('user c', 'c.userid = user.createdby', 'left')
            ->join('user up', 'up.userid = user.updatedby', 'left')
            ->join('user d', 'd.userid = user.deletedby', 'left')
            ->where('user.isdeleted', 0)
            ->orderBy('createddate', 'DESC');
    }
}
