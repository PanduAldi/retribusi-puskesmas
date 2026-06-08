<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisRetribusiModel extends Model
{
    protected $table            = 'jenis_retribusi';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['jenis'];
    protected $useTimestamps    = false;
}
