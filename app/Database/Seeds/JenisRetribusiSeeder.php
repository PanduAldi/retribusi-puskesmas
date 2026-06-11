<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JenisRetribusiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['jenis' => 'Pemeriksaan Umum'],
            ['jenis' => 'Pemeriksaan Gigi'],
            ['jenis' => 'Laboratorium'],
            ['jenis' => 'Rawat Inap'],
            ['jenis' => 'Tindakan Medis'],
            ['jenis' => 'Konsultasi Gizi'],
            ['jenis' => 'Imunisasi'],
            ['jenis' => 'Surat Keterangan Sehat'],
        ];

        $this->db->table('jenis_retribusi')->insertBatch($data);
    }
}