<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasienTable extends Migration
{
    public function up()
    {
        // 1. Buat tabel pasien
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'no_rm'         => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'nama_pasien'   => ['type' => 'VARCHAR', 'constraint' => 150],
            'alamat_pasien' => ['type' => 'TEXT', 'null' => true],
            'jenis_kelamin' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'tgl_lahir'     => ['type' => 'DATE', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pasien');

        // 2. Migrasi data existing dari transaksi_retribusi ke pasien
        $db = \Config\Database::connect();
        $existing = $db->table('transaksi_retribusi')
            ->select('no_dokumen, nama_pasien, alamat_pasien, jenis_kelamin, tgl_lahir')
            ->where('nama_pasien IS NOT NULL')
            ->where('nama_pasien !=', '')
            ->get()
            ->getResultArray();

        foreach ($existing as $row) {
            $db->table('pasien')->insert([
                'no_rm'         => $row['no_dokumen'],
                'nama_pasien'   => $row['nama_pasien'],
                'alamat_pasien' => $row['alamat_pasien'],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'tgl_lahir'     => $row['tgl_lahir'],
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // 3. Tambah id_pasien ke transaksi_retribusi, lalu mapping
        $this->forge->addColumn('transaksi_retribusi', [
            'id_pasien' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'id_puskesmas'],
        ]);

        // Set id_pasien berdasarkan no_dokumen
        $db->query("UPDATE transaksi_retribusi tr
                    JOIN pasien p ON p.no_rm = tr.no_dokumen
                    SET tr.id_pasien = p.id");

        // 4. Foreign key
        $this->forge->addForeignKey('id_pasien', 'pasien', 'id', 'SET NULL', 'CASCADE');

        // 5. Drop kolom pasien dari transaksi_retribusi
        $cols = ['nama_pasien', 'alamat_pasien', 'jenis_kelamin', 'tgl_lahir'];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'transaksi_retribusi')) {
                $this->forge->dropColumn('transaksi_retribusi', $col);
            }
        }
    }

    public function down()
    {
        // Restore kolom
        $this->forge->addColumn('transaksi_retribusi', [
            'nama_pasien'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'alamat_pasien' => ['type' => 'TEXT', 'null' => true],
            'jenis_kelamin' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'tgl_lahir'     => ['type' => 'DATE', 'null' => true],
        ]);

        // Restore data dari pasien ke transaksi_retribusi
        $db = \Config\Database::connect();
        $db->query("UPDATE transaksi_retribusi tr
                    JOIN pasien p ON p.id = tr.id_pasien
                    SET tr.nama_pasien = p.nama_pasien,
                        tr.alamat_pasien = p.alamat_pasien,
                        tr.jenis_kelamin = p.jenis_kelamin,
                        tr.tgl_lahir = p.tgl_lahir");

        $this->forge->dropForeignKey('transaksi_retribusi', 'transaksi_retribusi_id_pasien_foreign');
        $this->forge->dropColumn('transaksi_retribusi', 'id_pasien');
        $this->forge->dropTable('pasien');
    }
}