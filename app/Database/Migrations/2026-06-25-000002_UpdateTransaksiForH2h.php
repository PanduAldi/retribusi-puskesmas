<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTransaksiForH2h extends Migration
{
    public function up()
    {
        // Tambah kolom pada transaksi_retribusi jika belum ada
        $fieldsToAdd = [];
        
        if (!$this->db->fieldExists('tgl_penetapan', 'transaksi_retribusi')) {
            $fieldsToAdd['tgl_penetapan'] = ['type' => 'DATE', 'null' => true];
        }
        if (!$this->db->fieldExists('tgl_jatuh_tempo', 'transaksi_retribusi')) {
            $fieldsToAdd['tgl_jatuh_tempo'] = ['type' => 'DATE', 'null' => true];
        }
        if (!$this->db->fieldExists('noreff_bank', 'transaksi_retribusi')) {
            $fieldsToAdd['noreff_bank'] = ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true];
        }
        if (!$this->db->fieldExists('channel', 'transaksi_retribusi')) {
            $fieldsToAdd['channel'] = ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true];
        }
        if (!$this->db->fieldExists('device', 'transaksi_retribusi')) {
            $fieldsToAdd['device'] = ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('transaksi_retribusi', $fieldsToAdd);
        }
    }

    public function down()
    {
        $cols = ['tgl_penetapan', 'tgl_jatuh_tempo', 'noreff_bank', 'channel', 'device'];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'transaksi_retribusi')) {
                $this->forge->dropColumn('transaksi_retribusi', $col);
            }
        }
    }
}
