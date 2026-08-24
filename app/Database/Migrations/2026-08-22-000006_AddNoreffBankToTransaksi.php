<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoreffBankToTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaksi_retribusi', [
            'noreff_bank' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'id_billing',
            ],
            'bank_status'  => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'noreff_bank',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi_retribusi', ['noreff_bank', 'bank_status']);
    }
}