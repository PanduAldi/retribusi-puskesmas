<?php

namespace App\Models;

use CodeIgniter\Model;

class PuskesmasJenisModel extends Model
{
    protected $table            = 'puskesmas_jenis';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_puskesmas', 'id_jenis'];

    /**
     * Get IDs of service types (jenis_retribusi) allowed for a specific puskesmas
     */
    public function getJenisIdsByPuskesmas(int $idPuskesmas): array
    {
        $rows = $this->where('id_puskesmas', $idPuskesmas)->findAll();
        return array_column($rows, 'id_jenis');
    }

    /**
     * Check if a specific service type is allowed for a specific puskesmas
     */
    public function isAllowed(int $idPuskesmas, int $idJenis): bool
    {
        return $this->where('id_puskesmas', $idPuskesmas)
                    ->where('id_jenis', $idJenis)
                    ->countAllResults() > 0;
    }

    /**
     * Sync mappings for a puskesmas: replace existing with new set
     */
    public function syncMappings(int $idPuskesmas, array $newJenisIds)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Delete existing
        $this->where('id_puskesmas', $idPuskesmas)->delete();

        // Insert new
        if (!empty($newJenisIds)) {
            $data = array_map(function($idJenis) use ($idPuskesmas) {
                return [
                    'id_puskesmas' => $idPuskesmas,
                    'id_jenis'     => $idJenis
                ];
            }, $newJenisIds);
            $this->insertBatch($data);
        }

        $db->transComplete();
        return $db->transStatus();
    }
}
