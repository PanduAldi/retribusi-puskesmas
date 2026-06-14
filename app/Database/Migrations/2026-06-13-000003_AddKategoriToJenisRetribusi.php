<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriToJenisRetribusi extends Migration
{
    public function up()
    {
        $fields = [
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'id',
            ],
        ];
        $this->forge->addColumn('jenis_retribusi', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('jenis_retribusi', 'kategori');
    }
}
