<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiItemModel extends Model
{
    protected $table            = 'transaksi_item';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_transaksi',
        'id_jenis',
        'volume',
        'amount'
    ];

    public function getItemsByTransaksi(int $idTransaksi)
    {
        return $this->select('transaksi_item.*, jenis_retribusi.jenis')
            ->join('jenis_retribusi', 'jenis_retribusi.id = transaksi_item.id_jenis')
            ->where('id_transaksi', $idTransaksi)
            ->findAll();
    }
}
