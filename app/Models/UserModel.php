<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'username',
        'password_hash',
        'nama',
        'role',
        'id_puskesmas',
        'is_active',
        'last_login_at'
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
