<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // PostgreSQL: nama tabel kamu adalah "user" (reserved word), aman pakai quote di query join,
    // tapi untuk table property cukup "user" biasanya tetap bisa. Kalau error, ganti jadi '"user"'.
    protected $table      = 'user';
    protected $primaryKey = 'userid';

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'username',
        'userlevelid',
        'password',
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

    public function getUser()
    {
        return $this->select('user.userid, user.username, user.nama, user.isdeleted,
                          user.createdby, user.createddate, user.updatedby, user.updateddate, 
                          user.deletedby, user.deleteddate,
                          c.nama as createdby_name, 
                          up.nama as updatedby_name, 
                          d.nama as deletedby_name')
            ->join('"user" c', 'c.userid = "user".createdby', 'left')
            ->join('"user" up', 'up.userid = "user".updatedby', 'left')
            ->join('"user" d', 'd.userid = "user".deletedby', 'left')
            ->orderBy('user.createddate', 'DESC');
    }
}
