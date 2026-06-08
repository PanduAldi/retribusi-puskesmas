<?php

namespace App\Models;

use CodeIgniter\Model;

class BillModel extends Model
{
    protected $table            = 'bill';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_billing'];
    protected $useTimestamps    = false;

    public function existsByIdBilling(string $idBilling): bool
    {
        return $this->where('id_billing', $idBilling)->first() !== null;
    }
}
