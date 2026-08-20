<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table            = 'pasien';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['no_rm', 'nama_pasien', 'alamat_pasien', 'jenis_kelamin', 'tgl_lahir'];
    protected $useTimestamps    = true;

    public function findByNoRm(string $noRm)
    {
        return $this->where('no_rm', $noRm)->first();
    }

    public function createOrUpdate(string $noRm, array $data)
    {
        $pasien = $this->findByNoRm($noRm);
        if ($pasien) {
            $this->update($pasien['id'], $data);
            return $pasien['id'];
        }

        $data['no_rm'] = $noRm;
        $this->insert($data);
        return $this->insertID();
    }
}
