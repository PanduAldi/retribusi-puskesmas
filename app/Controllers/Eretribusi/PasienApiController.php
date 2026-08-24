<?php

namespace App\Controllers\Eretribusi;

use App\Controllers\BaseController;
use App\Models\PasienModel;

class PasienApiController extends BaseController
{
    public function getPasien($noRm)
    {
        $pasien = (new PasienModel())->findByNoRm($noRm);

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