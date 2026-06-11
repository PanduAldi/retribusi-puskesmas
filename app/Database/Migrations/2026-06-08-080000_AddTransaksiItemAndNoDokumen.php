<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransaksiItemAndNoDokumen extends Migration
{
    public function up()
    {
        // 1. Tambah kolom no_dokumen ke transaksi_retribusi
        if (!$this->db->fieldExists('no_dokumen', 'transaksi_retribusi')) {
            $fields = [
                'no_dokumen' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                    'after'      => 'id_puskesmas'
                ],
            ];
            $this->forge->addColumn('transaksi_retribusi', $fields);
        }

        // 2. Buat tabel transaksi_item untuk multi jenis retribusi
        if (!$this->db->tableExists('transaksi_item')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'id_transaksi' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'id_jenis' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
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
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('id_transaksi', 'transaksi_retribusi', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('id_jenis', 'jenis_retribusi', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('transaksi_item');
        }

        // 3. Hapus kolom lama dari transaksi_retribusi agar ter-normalisasi
        // Cek dulu apakah FK masih ada
        $fkExists = false;
        try {
            $this->db->query('ALTER TABLE `transaksi_retribusi` DROP FOREIGN KEY `transaksi_retribusi_id_jenis_foreign`');
            $fkExists = true;
        } catch (\Exception $e) {
            // FK might already be dropped
        }

        if ($this->db->fieldExists('id_jenis', 'transaksi_retribusi')) {
            $this->forge->dropColumn('transaksi_retribusi', 'id_jenis');
        }
        if ($this->db->fieldExists('volume', 'transaksi_retribusi')) {
            $this->forge->dropColumn('transaksi_retribusi', 'volume');
        }
        if ($this->db->fieldExists('amount', 'transaksi_retribusi')) {
            $this->forge->dropColumn('transaksi_retribusi', 'amount');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('transaksi_item')) {
            $this->forge->dropTable('transaksi_item');
        }

        if ($this->db->fieldExists('no_dokumen', 'transaksi_retribusi')) {
            $this->forge->dropColumn('transaksi_retribusi', 'no_dokumen');
        }

        // Kembalikan kolom yang dihapus (untuk rollback)
        if (!$this->db->fieldExists('id_jenis', 'transaksi_retribusi')) {
            $this->forge->addColumn('transaksi_retribusi', [
                'id_jenis' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'volume'   => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 1.00],
                'amount'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            ]);
            $this->forge->addForeignKey('id_jenis', 'jenis_retribusi', 'id', 'CASCADE', 'CASCADE');
        }
    }
}
