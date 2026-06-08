<?php

namespace App\Models;

use CodeIgniter\Model;

class TarifRetribusiModel extends Model
{
    protected $table            = 'tarif_retribusi';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_puskesmas', 'id_jenis', 'tarif'];
    protected $useTimestamps    = false;

    public function getTarifByPuskesmas(int $idPuskesmas): array
    {
        return $this->select('tarif_retribusi.*, jenis_retribusi.jenis')
            ->join('jenis_retribusi', 'jenis_retribusi.id = tarif_retribusi.id_jenis')
            ->where('tarif_retribusi.id_puskesmas', $idPuskesmas)
            ->findAll();
    }
}
