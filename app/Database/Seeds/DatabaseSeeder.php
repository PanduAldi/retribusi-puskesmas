<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('PuskesmasSeeder');
        $this->call('JenisRetribusiSeeder');
        $this->call('BrebesTarifSeeder');
        $this->call('UserSeeder');
    }
}
