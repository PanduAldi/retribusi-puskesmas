<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePuskesmasCodes extends Migration
{
    public function up()
    {
        $this->db->query("
            UPDATE puskesmas SET kode_retribusi = CASE prasarana
                WHEN 'Puskesmas Salem' THEN 'P3329010101'
                WHEN 'Puskesmas Bentar' THEN 'P3329010202'
                WHEN 'Puskesmas Bantarkawung' THEN 'P3329020101'
                WHEN 'Puskesmas Buaran' THEN 'P3329020202'
                WHEN 'Puskesmas Bumiayu' THEN 'P3329030101'
                WHEN 'Puskesmas Kaliwadas' THEN 'P3329030202'
                WHEN 'Puskesmas Paguyangan' THEN 'P3329040101'
                WHEN 'Puskesmas Winduaji' THEN 'P3329040202'
                WHEN 'Puskesmas Sirampog' THEN 'P3329050101'
                WHEN 'Puskesmas Tonjong' THEN 'P3329060101'
                WHEN 'Puskesmas Kutamendala' THEN 'P3329060202'
                WHEN 'Puskesmas Larangan' THEN 'P3329070101'
                WHEN 'Puskesmas Sitanggal' THEN 'P3329070202'
                WHEN 'Puskesmas Ketanggungan' THEN 'P3329080101'
                WHEN 'Puskesmas Cikeusal Kidul' THEN 'P3329080102'
                WHEN 'Puskesmas Banjarharjo' THEN 'P3329090101'
                WHEN 'Puskesmas Bandungsari' THEN 'P3329090103'
                WHEN 'Puskesmas Cikakak' THEN 'P3329090202'
                WHEN 'Puskesmas Losari' THEN 'P3329100101'
                WHEN 'Puskesmas Bojongsari' THEN 'P3329100102'
                WHEN 'Puskesmas Kecipir' THEN 'P3329100103'
                WHEN 'Puskesmas Tanjung' THEN 'P3329110101'
                WHEN 'Puskesmas Kemurang Wetan' THEN 'P3329110202'
                WHEN 'Puskesmas Luwunggede' THEN 'P3329110203'
                WHEN 'Puskesmas Kersana' THEN 'P3329120201'
                WHEN 'Puskesmas Kluwut' THEN 'P3329130101'
                WHEN 'Puskesmas Bulakamba' THEN 'P3329130102'
                WHEN 'Puskesmas Siwuluh' THEN 'P3329130203'
                WHEN 'Puskesmas Wanasari' THEN 'P3329140201'
                WHEN 'Puskesmas Jagalempeni' THEN 'P3329140202'
                WHEN 'Puskesmas Sidamulya' THEN 'P3329140203'
                WHEN 'Puskesmas Jatirokeh' THEN 'P3329150101'
                WHEN 'Puskesmas Jatibarang' THEN 'P3329160101'
                WHEN 'Puskesmas Klikiran' THEN 'P3329160202'
                WHEN 'Puskesmas Brebes' THEN 'P3329170201'
                WHEN 'Puskesmas Pemaron' THEN 'P3329170202'
                WHEN 'Puskesmas Kalimati' THEN 'P3329170203'
                WHEN 'Puskesmas Kaligangsa' THEN 'P3329170204'
                ELSE kode_retribusi
            END
        ");
    }

    public function down()
    {
        // Optionally revert to old codes if needed, but we can leave empty or set to NULL.
        $this->db->query("
            UPDATE puskesmas SET kode_retribusi = CASE prasarana
                WHEN 'Puskesmas Salem' THEN '08004'
                WHEN 'Puskesmas Bentar' THEN '08005'
                WHEN 'Puskesmas Bantarkawung' THEN '08006'
                WHEN 'Puskesmas Buaran' THEN '08007'
                WHEN 'Puskesmas Bumiayu' THEN '08008'
                WHEN 'Puskesmas Kaliwadas' THEN '08009'
                WHEN 'Puskesmas Paguyangan' THEN '08010'
                WHEN 'Puskesmas Winduaji' THEN '08011'
                WHEN 'Puskesmas Sirampog' THEN '08012'
                WHEN 'Puskesmas Tonjong' THEN '08013'
                WHEN 'Puskesmas Kutamendala' THEN '08014'
                WHEN 'Puskesmas Larangan' THEN '08015'
                WHEN 'Puskesmas Sitanggal' THEN '08016'
                WHEN 'Puskesmas Ketanggungan' THEN '08017'
                WHEN 'Puskesmas Cikeusal Kidul' THEN '08018'
                WHEN 'Puskesmas Banjarharjo' THEN '08019'
                WHEN 'Puskesmas Bandungsari' THEN '08020'
                WHEN 'Puskesmas Cikakak' THEN '08021'
                WHEN 'Puskesmas Losari' THEN '08022'
                WHEN 'Puskesmas Bojongsari' THEN '08023'
                WHEN 'Puskesmas Kecipir' THEN '08024'
                WHEN 'Puskesmas Tanjung' THEN '08025'
                WHEN 'Puskesmas Kemurang Wetan' THEN '08026'
                WHEN 'Puskesmas Luwunggede' THEN '08027'
                WHEN 'Puskesmas Kersana' THEN '08028'
                WHEN 'Puskesmas Kluwut' THEN '08029'
                WHEN 'Puskesmas Bulakamba' THEN '08030'
                WHEN 'Puskesmas Siwuluh' THEN '08031'
                WHEN 'Puskesmas Wanasari' THEN '08032'
                WHEN 'Puskesmas Jagalempeni' THEN '08033'
                WHEN 'Puskesmas Sidamulya' THEN '08034'
                WHEN 'Puskesmas Jatirokeh' THEN '08035'
                WHEN 'Puskesmas Jatibarang' THEN '08036'
                WHEN 'Puskesmas Klikiran' THEN '08037'
                WHEN 'Puskesmas Brebes' THEN '08038'
                WHEN 'Puskesmas Pemaron' THEN '08039'
                WHEN 'Puskesmas Kalimati' THEN '08040'
                WHEN 'Puskesmas Kaligangsa' THEN '08041'
                ELSE kode_retribusi
            END
        ");
    }
}