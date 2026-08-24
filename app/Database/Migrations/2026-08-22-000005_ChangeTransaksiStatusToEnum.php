<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeTransaksiStatusToEnum extends Migration
{
    public function up()
    {
        // Ensure existing rows have valid values; if not, convert them
        $db = \Config\Database::connect();

        // Update rows where status is not 'pending' or 'paid' to 'pending' (safest default)
        $db = \Config\Database::connect();
        $db->table('transaksi_retribusi')
            ->where('status', '<>', 'pending')
            ->where('status', '<>', 'paid')
            ->update(['status' => 'pending']);

        // Modify column to ENUM
        $this->forge->modifyColumn('transaksi_retribusi', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'paid'],
                'default' => 'pending',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Revert to original type (e.g., VARCHAR)
        $this->forge->modifyColumn('transaksi_retribusi', [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
        ]);
    }
}