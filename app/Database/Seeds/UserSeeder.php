<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'      => 'admin_puskesmas1',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'nama'          => 'Admin Puskesmas Katang',
                'role'          => 'admin_puskesmas',
                'id_puskesmas'  => 1,
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'username'      => 'petugas1',
                'password_hash' => password_hash('petugas123', PASSWORD_DEFAULT),
                'nama'          => 'Petugas Puskesmas Katang',
                'role'          => 'petugas',
                'id_puskesmas'  => 1,
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'username'      => 'admin_kabupaten',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'nama'          => 'Administrator Kabupaten',
                'role'          => 'admin_kabupaten',
                'id_puskesmas'  => null,
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
