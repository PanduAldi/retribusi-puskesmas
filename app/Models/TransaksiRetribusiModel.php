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
        'no_dokumen',
        'invoice',
        'invoice_date',
        'id_billing',
        'status'
    ];
    protected $useTimestamps    = false;

    public function getByInvoice(string $invoice): array
    {
        return $this->select('transaksi_retribusi.*')
            ->where('transaksi_retribusi.invoice', $invoice)
            ->first() ?? [];
    }

    public function isInvoiceExists(string $invoice): bool
    {
        return $this->where('invoice', $invoice)->first() !== null;
    }
}
