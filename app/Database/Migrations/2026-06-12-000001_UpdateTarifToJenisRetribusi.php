<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTarifToJenisRetribusi extends Migration
{
    public function up()
    {
        // 1. Tambahkan kolom tarif ke jenis_retribusi
        $this->forge->addColumn('jenis_retribusi', [
            'tarif' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'after'      => 'jenis',
            ],
        ]);

        // 2. Migrasi data tarif yang sudah ada (jika ada)
        $db = \Config\Database::connect();
        $tarifData = $db->table('tarif_retribusi')->get()->getResultArray();

        foreach ($tarifData as $row) {
            $db->table('jenis_retribusi')
                ->where('id', $row['id_jenis'])
                ->update(['tarif' => $row['tarif']]);
        }

        // 3. Hapus tabel tarif_retribusi
        // Gunakan true pada parameter kedua dropTable untuk menghapus foreign key check di MySQL jika perlu
        $this->forge->dropTable('tarif_retribusi', true);
    }

    public function down()
    {
        // 1. Kembalikan tabel tarif_retribusi
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

        // 2. Hapus kolom tarif di jenis_retribusi
        $this->forge->dropColumn('jenis_retribusi', 'tarif');
    }
}
