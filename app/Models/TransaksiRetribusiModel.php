<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiRetribusiModel extends Model
{
    protected $table = 'transaksi_retribusi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_puskesmas',
        'no_dokumen',
        'nama_pasien',
        'alamat_pasien',
        'jenis_kelamin',
        'tgl_lahir',
        'invoice',
        'invoice_date',
        'status',
        'noreff_bank',
        'channel',
        'device',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = false;

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
