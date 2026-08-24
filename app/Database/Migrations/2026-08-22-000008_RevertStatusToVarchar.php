<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Production-safe: revert ENUM ke VARCHAR supaya nilai 'lunas' yg sudah ada tidak hilang.
 * Tidak menghapus kode/kolom yang sudah ada.
 */
class RevertStatusToVarchar extends Migration
{
    public function up()
    {
        // Cek tipe saat ini
        $db    = \Config\Database::connect();
        $row   = $db->query("SHOW COLUMNS FROM transaksi_retribusi WHERE Field = 'status'")->getRow();
        $type  = strtolower($row->Type ?? '');

        // Jika sudah VARCHAR, skip
        if (str_starts_with($type, 'varchar')) {
            return;
        }

        // ENUM → VARCHAR (pertahankan semua nilai: pending, paid, lunas)
        $db->query("ALTER TABLE transaksi_retribusi MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Kembali ke ENUM 3 nilai (lunas tetap ada)
        \Config\Database::connect()->query(
            "ALTER TABLE transaksi_retribusi MODIFY COLUMN status ENUM('pending','paid','lunas') NOT NULL DEFAULT 'pending'"
        );
    }
}