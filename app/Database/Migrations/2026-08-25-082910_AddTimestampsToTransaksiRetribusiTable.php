<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimestampsToTransaksiRetribusiTable extends Migration
{
    public function up()
    {
        $fields = [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('transaksi_retribusi', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi_retribusi', ['created_at', 'updated_at', 'deleted_at']);
    }
}
