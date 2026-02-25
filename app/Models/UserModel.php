<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'user';
    protected $primaryKey = 'userid';

    protected $returnType       = 'array';
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

    /**
     * Builder untuk list user lengkap dengan nama level + created/updated/deleted by.
     * PENTING: Ini return Query Builder dari $db->table(), bukan Model builder.
     * Jadi panggilnya: ->get()->getResultArray()
     */
    public function getUser()
    {
        return $this->db->table('"user" u')
            ->select('
                u.userid,
                u.username,
                u.nama,
                u.userlevelid,
                ul.userlevel AS userlevel_name,
                u.isdeleted,
                u.createdby,
                u.createddate,
                u.updatedby,
                u.updateddate,
                u.deletedby,
                u.deleteddate,
                c.nama  AS createdby_name,
                up.nama AS updatedby_name,
                d.nama  AS deletedby_name
            ')
            ->join('userlevel ul', 'ul.userlevelid = u.userlevelid', 'left')
            ->join('"user" c',  'c.userid  = u.createdby', 'left')
            ->join('"user" up', 'up.userid = u.updatedby', 'left')
            ->join('"user" d',  'd.userid  = u.deletedby', 'left')
            ->orderBy('u.createddate', 'DESC');
    }
}