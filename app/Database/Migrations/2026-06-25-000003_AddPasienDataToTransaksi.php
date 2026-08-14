<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasienDataToTransaksi extends Migration
{
    public function up()
    {
        $fields = [
            'nama_pasien'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'no_dokumen'],
            'alamat_pasien' => ['type' => 'TEXT', 'null' => true, 'after' => 'nama_pasien'],
            'jenis_kelamin' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'after' => 'alamat_pasien'],
            'tgl_lahir'     => ['type' => 'DATE', 'null' => true, 'after' => 'jenis_kelamin'],
        ];

        $this->forge->addColumn('transaksi_retribusi', $fields);
    }

    public function down()
    {
        $cols = ['nama_pasien', 'alamat_pasien', 'jenis_kelamin', 'tgl_lahir'];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'transaksi_retribusi')) {
                $this->forge->dropColumn('transaksi_retribusi', $col);
            }
        }
    }
}
