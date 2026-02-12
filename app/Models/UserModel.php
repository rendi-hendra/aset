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
        // NOTE: join "user" harus di-quote di PostgreSQL
        return $this->select('u.userid, u.username, u.nama, u.userlevelid, u.isdeleted,
                              u.createdby, u.createddate, u.updatedby, u.updateddate, u.deletedby, u.deleteddate,
                              c.nama as createdby_name, up.nama as updatedby_name, d.nama as deletedby_name,
                              ul.userlevel as userlevel_name')
            ->from('"user" u')
            ->join('"user" c', 'c.userid = u.createdby', 'left')
            ->join('"user" up', 'up.userid = u.updatedby', 'left')
            ->join('"user" d', 'd.userid = u.deletedby', 'left')
            ->join('userlevel ul', 'ul.userlevelid = u.userlevelid', 'left')
            ->where('u.isdeleted', 0)
            ->orderBy('u.createddate', 'DESC');
    }
}
