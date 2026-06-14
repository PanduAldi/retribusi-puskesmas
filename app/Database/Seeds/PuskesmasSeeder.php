<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PuskesmasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'prasarana'      => 'Puskesmas Brebes',
                'kode_retribusi' => '11001',
            ],
            [
                'prasarana'      => 'Puskesmas Wanasari',
                'kode_retribusi' => '11002',
            ],
        ];

        foreach ($data as $row) {
            $exists = $this->db->table('puskesmas')
                ->where('prasarana', $row['prasarana'])
                ->countAllResults();

            if (!$exists) {
                $this->db->table('puskesmas')->insert($row);
            }
        }
    }
}
