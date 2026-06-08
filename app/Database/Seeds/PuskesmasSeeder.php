<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PuskesmasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'prasarana'      => 'Puskesmas Katang',
                'kode_retribusi' => 'PK001',
            ],
            [
                'prasarana'      => 'Puskesmas Baringin',
                'kode_retribusi' => 'PK002',
            ],
        ];
        // Insert batch
        $this->db->table('puskesmas')->insertBatch($data);
    }
}
