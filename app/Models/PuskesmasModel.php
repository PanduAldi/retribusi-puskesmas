<?php

namespace App\Models;

use CodeIgniter\Model;

class PuskesmasModel extends Model
{
    protected $table            = 'puskesmas';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['prasarana', 'kode_retribusi'];
    protected $useTimestamps    = false;
}
