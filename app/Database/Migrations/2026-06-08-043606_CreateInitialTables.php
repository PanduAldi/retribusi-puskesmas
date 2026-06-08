<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInitialTables extends Migration
{
    public function up()
    {
        // Tabel: puskesmas (atau tempat)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'prasarana' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'kode_retribusi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('puskesmas');

        // Tabel: jenis_retribusi
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('jenis_retribusi');

        // Tabel: tarif_retribusi
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
            'tarif' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_puskesmas', 'puskesmas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jenis', 'jenis_retribusi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tarif_retribusi');

        // Tabel: bill (billing)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_billing' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('bill');

        // Tabel: transaksi_retribusi
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
            'invoice' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'invoice_date' => [
                'type' => 'DATE',
            ],
            'id_billing' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'volume' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 1.00,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0, // 0 = belum bayar, 1 = sudah bayar
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_puskesmas', 'puskesmas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jenis', 'jenis_retribusi', 'id', 'CASCADE', 'CASCADE');
        // id_billing is unique in 'bill' table, but here it's a soft reference
        $this->forge->createTable('transaksi_retribusi');

        // Tabel: users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'id_puskesmas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_puskesmas', 'puskesmas', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
        $this->forge->dropTable('transaksi_retribusi');
        $this->forge->dropTable('bill');
        $this->forge->dropTable('tarif_retribusi');
        $this->forge->dropTable('jenis_retribusi');
        $this->forge->dropTable('puskesmas');
    }
}
