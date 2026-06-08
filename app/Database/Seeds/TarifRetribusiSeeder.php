<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TarifRetribusiSeeder extends Seeder
{
    public function run()
    {
        // Sample tariffs for Puskesmas 1 and 2
        $data = [
            // PK001 (Puskesmas Katang)
            ['id_puskesmas' => 1, 'id_jenis' => 1, 'tarif' => 5000],
            ['id_puskesmas' => 1, 'id_jenis' => 2, 'tarif' => 10000],
            ['id_puskesmas' => 1, 'id_jenis' => 3, 'tarif' => 15000],
            ['id_puskesmas' => 1, 'id_jenis' => 8, 'tarif' => 10000],

            // PK002 (Puskesmas Baringin)
            ['id_puskesmas' => 2, 'id_jenis' => 1, 'tarif' => 6000],
            ['id_puskesmas' => 2, 'id_jenis' => 2, 'tarif' => 12000],
            ['id_puskesmas' => 2, 'id_jenis' => 4, 'tarif' => 50000],
        ];

        $this->db->table('tarif_retribusi')->insertBatch($data);
    }
}
