<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PuskesmasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['prasarana' => 'Puskesmas Salem', 'kode_retribusi' => '08004'],
            ['prasarana' => 'Puskesmas Bentar', 'kode_retribusi' => '08005'],
            ['prasarana' => 'Puskesmas Bantarkawung', 'kode_retribusi' => '08006'],
            ['prasarana' => 'Puskesmas Buaran', 'kode_retribusi' => '08007'],
            ['prasarana' => 'Puskesmas Bumiayu', 'kode_retribusi' => '08008'],
            ['prasarana' => 'Puskesmas Kaliwadas', 'kode_retribusi' => '08009'],
            ['prasarana' => 'Puskesmas Paguyangan', 'kode_retribusi' => '08010'],
            ['prasarana' => 'Puskesmas Winduaji', 'kode_retribusi' => '08011'],
            ['prasarana' => 'Puskesmas Sirampog', 'kode_retribusi' => '08012'],
            ['prasarana' => 'Puskesmas Tonjong', 'kode_retribusi' => '08013'],
            ['prasarana' => 'Puskesmas Kutamendala', 'kode_retribusi' => '08014'],
            ['prasarana' => 'Puskesmas Larangan', 'kode_retribusi' => '08015'],
            ['prasarana' => 'Puskesmas Sitanggal', 'kode_retribusi' => '08016'],
            ['prasarana' => 'Puskesmas Ketanggungan', 'kode_retribusi' => '08017'],
            ['prasarana' => 'Puskesmas Cikeusal Kidul', 'kode_retribusi' => '08018'],
            ['prasarana' => 'Puskesmas Banjarharjo', 'kode_retribusi' => '08019'],
            ['prasarana' => 'Puskesmas Bandungsari', 'kode_retribusi' => '08020'],
            ['prasarana' => 'Puskesmas Cikakak', 'kode_retribusi' => '08021'],
            ['prasarana' => 'Puskesmas Losari', 'kode_retribusi' => '08022'],
            ['prasarana' => 'Puskesmas Bojongsari', 'kode_retribusi' => '08023'],
            ['prasarana' => 'Puskesmas Kecipir', 'kode_retribusi' => '08024'],
            ['prasarana' => 'Puskesmas Tanjung', 'kode_retribusi' => '08025'],
            ['prasarana' => 'Puskesmas Kemurang Wetan', 'kode_retribusi' => '08026'],
            ['prasarana' => 'Puskesmas Luwunggede', 'kode_retribusi' => '08027'],
            ['prasarana' => 'Puskesmas Kersana', 'kode_retribusi' => '08028'],
            ['prasarana' => 'Puskesmas Kluwut', 'kode_retribusi' => '08029'],
            ['prasarana' => 'Puskesmas Bulakamba', 'kode_retribusi' => '08030'],
            ['prasarana' => 'Puskesmas Siwuluh', 'kode_retribusi' => '08031'],
            ['prasarana' => 'Puskesmas Wanasari', 'kode_retribusi' => '08032'],
            ['prasarana' => 'Puskesmas Jagalempeni', 'kode_retribusi' => '08033'],
            ['prasarana' => 'Puskesmas Sidamulya', 'kode_retribusi' => '08034'],
            ['prasarana' => 'Puskesmas Jatirokeh', 'kode_retribusi' => '08035'],
            ['prasarana' => 'Puskesmas Jatibarang', 'kode_retribusi' => '08036'],
            ['prasarana' => 'Puskesmas Klikiran', 'kode_retribusi' => '08037'],
            ['prasarana' => 'Puskesmas Brebes', 'kode_retribusi' => '08038'],
            ['prasarana' => 'Puskesmas Pemaron', 'kode_retribusi' => '08039'],
            ['prasarana' => 'Puskesmas Kalimati', 'kode_retribusi' => '08040'],
            ['prasarana' => 'Puskesmas Kaligangsa', 'kode_retribusi' => '08041'],
        ];

        foreach ($data as $row) {
            $builder = $this->db->table('puskesmas');
            $exists = $builder
                ->where('prasarana', $row['prasarana'])
                ->countAllResults();

            if ($exists) {
                $this->db->table('puskesmas')
                    ->where('prasarana', $row['prasarana'])
                    ->update(['kode_retribusi' => $row['kode_retribusi']]);

                continue;
            }

            $this->db->table('puskesmas')->insert($row);
        }
    }
}