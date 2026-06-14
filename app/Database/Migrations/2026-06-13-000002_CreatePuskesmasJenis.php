<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePuskesmasJenis extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_puskesmas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_jenis' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['id_puskesmas', 'id_jenis']);
        $this->forge->addForeignKey('id_puskesmas', 'puskesmas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jenis', 'jenis_retribusi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('puskesmas_jenis');
    }

    public function down()
    {
        $this->forge->dropTable('puskesmas_jenis');
    }
}
