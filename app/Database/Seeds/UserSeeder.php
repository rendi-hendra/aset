<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('user')->insert([
            'username' => 'rendi',
            'nama' => 'rendi',
            'createdby' => 1,
            'createddate' => date(now()),
            'password' => password_hash('rendi', PASSWORD_DEFAULT),
        ]);
    }
}
