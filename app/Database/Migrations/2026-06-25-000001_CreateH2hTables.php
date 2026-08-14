<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateH2hTables extends Migration
{
    public function up()
    {
        // Tabel Token H2H
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'token'      => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'expires_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('h2h_tokens', true);

        // Tabel Log / Audit Trail H2H
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'endpoint'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'request'     => ['type' => 'TEXT', 'null' => true],
            'response'    => ['type' => 'TEXT', 'null' => true],
            'ip_address'  => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('h2h_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('h2h_tokens', true);
        $this->forge->dropTable('h2h_logs', true);
    }
}
