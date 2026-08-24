<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoreffBankToTransaksi extends Migration
{
    public function up()
    {
        $fields = [];
        $existing = array_map('strtolower', \Config\Database::connect()->getFieldNames('transaksi_retribusi'));

        if (!in_array('noreff_bank', $existing, true)) {
            $fields['noreff_bank'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'id_billing',
            ];
        }
        if (!in_array('bank_status', $existing, true)) {
            $fields['bank_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'id_billing',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('transaksi_retribusi', $fields);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi_retribusi', ['noreff_bank', 'bank_status']);
    }
}