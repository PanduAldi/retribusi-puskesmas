<?php

namespace App\Controllers\Eretribusi;

use App\Controllers\BaseController;
use App\Models\TransaksiRetribusiModel;

class PasienApiController extends BaseController
{
    public function getPasien($noRm)
    {
        $model = new TransaksiRetribusiModel();
        // Cari data pasien terbaru berdasarkan no_dokumen (No RM)
        $pasien = $model->where('no_dokumen', $noRm)
            ->orderBy('id', 'DESC')
            ->first();

        if ($pasien) {
            return $this->response->setJSON([
                'status'        => 'success',
                'nama_pasien'   => $pasien['nama_pasien'],
                'alamat_pasien' => $pasien['alamat_pasien'],
                'jenis_kelamin' => $pasien['jenis_kelamin'],
                'tgl_lahir'     => $pasien['tgl_lahir']
            ]);
        }

        return $this->response->setJSON([
            'status' => 'not_found'
        ]);
    }
}
