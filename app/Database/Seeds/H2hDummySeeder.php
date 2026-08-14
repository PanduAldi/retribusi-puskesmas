<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class H2hDummySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Cek Puskesmas dummy jika belum ada
        $puskesmas = $db->table('puskesmas')->where('kode_retribusi', '3306001')->get()->getRowArray();
        if (!$puskesmas) {
            $db->table('puskesmas')->insert([
                'prasarana'      => 'Puskesmas Purworejo',
                'kode_retribusi' => '3306001'
            ]);
            $idPuskesmas = $db->insertID();
        } else {
            $idPuskesmas = $puskesmas['id'];
        }

        // 2. Cek Jenis Retribusi dummy
        $jenis = $db->table('jenis_retribusi')->where('jenis', 'Pemeriksaan Umum')->get()->getRowArray();
        if (!$jenis) {
            $db->table('jenis_retribusi')->insert([
                'kategori'   => 'Layanan Umum',
                'jenis'      => 'Pemeriksaan Umum',
                'tarif'      => 150000
            ]);
            $idJenis1 = $db->insertID();
        } else {
            $idJenis1 = $jenis['id'];
        }

        $jenis2 = $db->table('jenis_retribusi')->where('jenis', 'Pemeriksaan Gigi')->get()->getRowArray();
        if (!$jenis2) {
            $db->table('jenis_retribusi')->insert([
                'kategori'   => 'Layanan Gigi',
                'jenis'      => 'Pemeriksaan Gigi',
                'tarif'      => 120000
            ]);
            $idJenis2 = $db->insertID();
        } else {
            $idJenis2 = $jenis2['id'];
        }

        // 3. Buat Transaksi Dummy dengan No. RM 2304002648 dan Data Pasien Lengkap
        $invoice = 'RET-3306001-' . date('ymd') . '-99887';
        
        $exist = $db->table('transaksi_retribusi')->where('no_dokumen', '2304002648')->where('status', 'pending')->get()->getRowArray();
        if (!$exist) {
            $db->table('transaksi_retribusi')->insert([
                'id_puskesmas'  => $idPuskesmas,
                'no_dokumen'    => '2304002648',
                'nama_pasien'   => 'MUHAMMAD ARIF WICAKSONO',
                'alamat_pasien' => 'Jl. Pahlawan No. 123 Purworejo',
                'jenis_kelamin' => 'LAKI-LAKI',
                'tgl_lahir'     => '1994-10-10',
                'invoice'       => $invoice,
                'invoice_date'  => date('Y-m-d'),
                'status'        => 'pending'
            ]);
            $idTrx = $db->insertID();

            // Insert Items
            $db->table('transaksi_item')->insertBatch([
                [
                    'id_transaksi' => $idTrx,
                    'id_jenis'     => $idJenis1,
                    'volume'       => 1,
                    'amount'       => 150000,
                ],
                [
                    'id_transaksi' => $idTrx,
                    'id_jenis'     => $idJenis2,
                    'volume'       => 1,
                    'amount'       => 120000,
                ]
            ]);
        }
    }
}
