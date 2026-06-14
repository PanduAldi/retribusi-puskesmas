<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BrebesTarifSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Karcis Pendaftaran
            ['kategori' => 'Pendaftaran', 'jenis' => 'Karcis Pendaftaran', 'tarif' => 7000],

            // UGD
            ['kategori' => 'UGD', 'jenis' => 'Bangsal UGD', 'tarif' => 20000],
            ['kategori' => 'UGD', 'jenis' => 'Tindakan IVFD (Pasang Infus)', 'tarif' => 9600],
            ['kategori' => 'UGD', 'jenis' => 'Asuhan Keperawatan UGD', 'tarif' => 9600],

            // Pelayanan Kebidanan
            ['kategori' => 'Pelayanan Kebidanan', 'jenis' => 'Persalinan Normal', 'tarif' => 600000],
            ['kategori' => 'Pelayanan Kebidanan', 'jenis' => 'Tarif retribusi untuk perawatan pada bayi baru lahir', 'tarif' => 25000],

            // Ruang Rawat Inap
            ['kategori' => 'Ruang Rawat Inap', 'jenis' => 'Biaya kamar per hari', 'tarif' => 30000],
            ['kategori' => 'Ruang Rawat Inap', 'jenis' => 'Visit Dokter', 'tarif' => 10000],
            ['kategori' => 'Ruang Rawat Inap', 'jenis' => 'Konsul Dokter', 'tarif' => 10000],
            ['kategori' => 'Ruang Rawat Inap', 'jenis' => 'Asuhan Keperawatan', 'tarif' => 10000],

            // Pemeriksaan Laboratorium (Lanjutan)
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Darah lengkap', 'tarif' => 70000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Hemoglobin', 'tarif' => 8000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Hematokrit', 'tarif' => 15000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Leukosit', 'tarif' => 13000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Eritrosit', 'tarif' => 13000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Trombosit', 'tarif' => 15000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Diff Count', 'tarif' => 13000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Laju Endap Darah', 'tarif' => 15000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Golongan Darah', 'tarif' => 10000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Glukosa', 'tarif' => 15000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Trygliserida', 'tarif' => 30000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Creatinin', 'tarif' => 20000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Urine Lengkap', 'tarif' => 20000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Faeces', 'tarif' => 15000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'HBSag', 'tarif' => 25000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Tes Kehamilan', 'tarif' => 10000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'SGOT', 'tarif' => 25000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'SGPT', 'tarif' => 25000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Kolesterol', 'tarif' => 25000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Ureum', 'tarif' => 20000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Asam urat', 'tarif' => 20000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Malaria', 'tarif' => 15000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Gonorhoe', 'tarif' => 10000],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'TB – BTA', 'tarif' => 0],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'HIV', 'tarif' => 0],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'Sypilis', 'tarif' => 0],
            ['kategori' => 'Pemeriksaan Laboratorium', 'jenis' => 'IMS', 'tarif' => 0],

            // Konsul Gizi
            ['kategori' => 'Konsul Gizi', 'jenis' => 'Konsul Gizi / Asupan Gizi', 'tarif' => 5000],

            // Unit Perawatan Gigi
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Penambalan gigi dewasa glass ionomer', 'tarif' => 32000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Penambalan gigi anak glass ionomer', 'tarif' => 22000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Penambalan sementara gangren', 'tarif' => 17000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Penambalan sementara urat syaraf', 'tarif' => 17000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Pencabutan gigi susu dengan injek/komplikasi', 'tarif' => 27000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Pencabutan gigi susu tanpa injeksi', 'tarif' => 12000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Pencabutan gigi dewasa tanpa komplikasi/ agar tunggal', 'tarif' => 32000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Pencabutan gigi dewasa dengan komplikasi/akar ganda', 'tarif' => 42000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Pembersihan karang gigi per regio gigi', 'tarif' => 32000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Grinding', 'tarif' => 22000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Trepanasi/ drainase abses', 'tarif' => 32000],
            ['kategori' => 'Unit Perawatan Gigi', 'jenis' => 'Tindakan kecil lainnya', 'tarif' => 12000],

            // Tindakan Medis Umum
            ['kategori' => 'Tindakan Medis & Paramedis', 'jenis' => 'Debridemen luka', 'tarif' => 20000],
            ['kategori' => 'Tindakan Medis & Paramedis', 'jenis' => 'Jahitan kurang dari 5', 'tarif' => 20000],
            ['kategori' => 'Tindakan Medis & Paramedis', 'jenis' => 'Jahitan 5 sampai 10', 'tarif' => 30000],
            ['kategori' => 'Tindakan Medis & Paramedis', 'jenis' => 'Jahitan lebih dari 10 (ditambah per jahitan Rp. 1.000)', 'tarif' => 35000],
            ['kategori' => 'Tindakan Medis & Paramedis', 'jenis' => 'Exterpasi corpus allenum', 'tarif' => 30000],
            ['kategori' => 'Tindakan Medis & Paramedis', 'jenis' => 'Luka bakar tanpa komplikasi', 'tarif' => 25000],

            // Tindakan Medis Kebidanan / Keluarga Berencana
            ['kategori' => 'Keluarga Berencana (KB)', 'jenis' => 'Pemasangan/pencabutan IUD', 'tarif' => 102000],
            ['kategori' => 'Keluarga Berencana (KB)', 'jenis' => 'Pemasangan/pencabutan implant', 'tarif' => 102000],
            ['kategori' => 'Keluarga Berencana (KB)', 'jenis' => 'Vasektomi', 'tarif' => 152000],
            ['kategori' => 'Keluarga Berencana (KB)', 'jenis' => 'Pelayanan suntik KB mandiri', 'tarif' => 17000],

            // Tindakan Medis Ringan
            ['kategori' => 'Tindakan Medis Ringan', 'jenis' => 'Tindakan daun telinga', 'tarif' => 31000],
            ['kategori' => 'Tindakan Medis Ringan', 'jenis' => 'Circumsisi', 'tarif' => 252000],
            ['kategori' => 'Tindakan Medis Ringan', 'jenis' => 'Pengambilan corpus aleum', 'tarif' => 32000],
            ['kategori' => 'Tindakan Medis Ringan', 'jenis' => 'Pengambilan atheum/lipoma/ganglion', 'tarif' => 152000],
            ['kategori' => 'Tindakan Medis Ringan', 'jenis' => 'Pemasangan cateter', 'tarif' => 21000],
            ['kategori' => 'Tindakan Medis Ringan', 'jenis' => 'Hordeolum', 'tarif' => 41000],

            // Surat Keterangan Dokter
            ['kategori' => 'Surat Keterangan', 'jenis' => 'Untuk pelajar/mahasiswa', 'tarif' => 5000],
            ['kategori' => 'Surat Keterangan', 'jenis' => 'Untuk mendapatkan pekerjaan', 'tarif' => 5000],
            ['kategori' => 'Surat Keterangan', 'jenis' => 'Untuk karyawan perusahan / lain - lain', 'tarif' => 10000],
        ];

        // Using Query Builder
        $this->db->table('jenis_retribusi')->insertBatch($data);
    }
}
