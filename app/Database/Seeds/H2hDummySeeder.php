<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class H2hDummySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Puskesmas Brebes (kode 08038, konsisten dgn PuskesmasSeeder)
        $puskesmas = $db->table('puskesmas')->where('kode_retribusi', '08038')->get()->getRowArray();
        if (!$puskesmas) {
            $db->table('puskesmas')->insert([
                'prasarana'      => 'Puskesmas Brebes',
                'kode_retribusi' => '08038'
            ]);
            $idPuskesmas = $db->insertID();
        } else {
            $idPuskesmas = $puskesmas['id'];
        }

        // 2. Jenis retribusi dummy
        $idJenis1 = $this->ensureJenis('Layanan Umum', 'Pemeriksaan Umum', 150000);
        $idJenis2 = $this->ensureJenis('Unit Perawatan Gigi', 'Pemeriksaan Gigi', 120000);

        // 3. Pasien dummy → tabel PASIEN (dipisah dari transaksi)
        $noRm = '2304002648';
        $pasien = $db->table('pasien')->where('no_rm', $noRm)->get()->getRowArray();
        if (!$pasien) {
            $db->table('pasien')->insert([
                'no_rm'         => $noRm,
                'nama_pasien'   => 'MUHAMMAD ARIF WICAKSONO',
                'alamat_pasien' => 'Jl. Pahlawan No. 123 Purworejo',
                'jenis_kelamin' => 'LAKI-LAKI',
                'tgl_lahir'     => '1994-10-10',
            ]);
            $idPasien = $db->insertID();
        } else {
            $idPasien = $pasien['id'];
        }

        // 4. Transaksi pending utk No RM tsb (tanpa kolom pasien)
        $exist = $db->table('transaksi_retribusi')
            ->where('id_pasien', $idPasien)
            ->where('status', 'pending')
            ->get()->getRowArray();
        if ($exist) {
            return; // sudah ada
        }

        $invoice = 'RET-08038-' . date('ymd') . '-99887';
        $idTrx = $db->table('transaksi_retribusi')->insert([
            'id_puskesmas'  => $idPuskesmas,
            'id_pasien'     => $idPasien,
            'no_dokumen'    => $noRm,
            'invoice'       => $invoice,
            'invoice_date'  => date('Y-m-d'),
            'status'        => 'pending',
        ]) ? $db->insertID() : null;
        if (!$idTrx) {
            $idTrx = $db->insertID();
        }

        // 5. Items
        $db->table('transaksi_item')->insertBatch([
            ['id_transaksi' => $idTrx, 'id_jenis' => $idJenis1, 'volume' => 1, 'amount' => 150000],
            ['id_transaksi' => $idTrx, 'id_jenis' => $idJenis2, 'volume' => 1, 'amount' => 120000],
        ]);
    }

    private function ensureJenis(string $kategori, string $jenis, int $tarif): int
    {
        $row = $this->db->table('jenis_retribusi')->where('jenis', $jenis)->get()->getRowArray();
        if ($row) {
            return (int) $row['id'];
        }
        $this->db->table('jenis_retribusi')->insert(compact('kategori', 'jenis', 'tarif'));
        return $this->db->insertID();
    }
}