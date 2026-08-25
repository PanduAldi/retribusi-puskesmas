<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiRetribusiModel extends Model
{
    protected $table = 'transaksi_retribusi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_puskesmas',
        'id_pasien',
        'no_dokumen',
        'invoice',
        'invoice_date',
        'status',
        'id_billing',
        'noreff_bank',
        'bank_status',
        'channel',
        'device',
        'paid_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    public function isInvoiceExists(string $invoice): bool
    {
        return $this->where('invoice', $invoice)->countAllResults() > 0;
    }

    public function getByInvoice(string $invoice)
    {
        return $this->where('invoice', $invoice)->first();
    }

    public function findByNoDokumen(string $noRm)
    {
        return $this->where('no_dokumen', $noRm)->orderBy('id', 'DESC')->first();
    }
}
