<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'      => 'admin_kabupaten',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'nama'          => 'Administrator Kabupaten',
                'role'          => 'admin_kabupaten',
                'pkm_name'      => null, // Helper for lookup
            ],
            [
                'username'      => 'puskesmas_brebes',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'nama'          => 'Admin Puskesmas Brebes',
                'role'          => 'admin_puskesmas',
                'pkm_name'      => 'Puskesmas Brebes',
            ],
            [
                'username'      => 'puskesmas.brebes.umum',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'nama'          => 'Loket Umum',
                'role'          => 'petugas',
                'pkm_name'      => 'Puskesmas Brebes',
            ],
            [
                'username'      => 'puskesmas.wanasari',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'nama'          => 'Puskesmas Wanasari',
                'role'          => 'admin_puskesmas',
                'pkm_name'      => 'Puskesmas Wanasari',
            ],
        ];

        foreach ($data as $row) {
            $exists = $this->db->table('users')
                ->where('username', $row['username'])
                ->countAllResults();

            if (!$exists) {
                $pkmId = null;
                if (!empty($row['pkm_name'])) {
                    $pkm = $this->db->table('puskesmas')
                        ->where('prasarana', $row['pkm_name'])
                        ->get()
                        ->getRow();
                    $pkmId = $pkm ? $pkm->id : null;
                }

                $insertData = [
                    'username'      => $row['username'],
                    'password_hash' => $row['password_hash'],
                    'nama'          => $row['nama'],
                    'role'          => $row['role'],
                    'id_puskesmas'  => $pkmId,
                    'is_active'     => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];

                $this->db->table('users')->insert($insertData);
            }
        }
    }
}
