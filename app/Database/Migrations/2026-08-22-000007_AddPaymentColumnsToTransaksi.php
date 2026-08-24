<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Idempotent: cek kolom existing dulu (aman utk production yg strukturnya berbeda).
 * Menambah kolom yang dipakai H2hController::payment() tapi belum ada di beberapa environment.
 */
class AddPaymentColumnsToTransaksi extends Migration
{
    public function up()
    {
        $existing = array_map('strtolower', \Config\Database::connect()->getFieldNames('transaksi_retribusi'));
        $fields = [];

        if (!in_array('channel', $existing, true)) {
            $fields['channel'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'bank_status',
            ];
        }
        if (!in_array('device', $existing, true)) {
            $fields['device'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'channel',
            ];
        }
        if (!in_array('paid_at', $existing, true)) {
            // ponytail: pakai paid_at (bukan updated_at) agar eksplisit kapan dibayar;
            // tabel ini tidak punya kolom timestamp standar.
            $fields['paid_at'] = [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'device',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('transaksi_retribusi', $fields);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi_retribusi', ['channel', 'device', 'paid_at']);
    }
}