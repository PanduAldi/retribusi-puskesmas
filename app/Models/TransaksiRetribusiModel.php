<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiRetribusiModel extends Model
{
    protected $table            = 'transaksi_retribusi';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_puskesmas',
        'id_jenis',
        'invoice',
        'invoice_date',
        'id_billing',
        'volume',
        'amount',
        'status'
    ];
    protected $useTimestamps    = false;

    public function getByInvoice(string $invoice): array
    {
        return $this->select('transaksi_retribusi.*, jenis_retribusi.jenis')
            ->join('jenis_retribusi', 'jenis_retribusi.id = transaksi_retribusi.id_jenis')
            ->where('transaksi_retribusi.invoice', $invoice)
            ->findAll();
    }

    public function isInvoiceExists(string $invoice): bool
    {
        return $this->where('invoice', $invoice)->first() !== null;
    }
}
