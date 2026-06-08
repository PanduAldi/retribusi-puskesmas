<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PuskesmasModel;
use App\Models\TransaksiRetribusiModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $puskesmasModel = new PuskesmasModel();
        $transaksiModel = new TransaksiRetribusiModel();

        $data = [
            'total_puskesmas' => $puskesmasModel->countAllResults(),
            'total_transaksi' => $transaksiModel->countAllResults(),
            'total_pendapatan' => $transaksiModel->selectSum('amount')->first()['amount'] ?? 0,
            'puskesmas_list' => $puskesmasModel->findAll(),
        ];

        return view('admin/dashboard', $data);
    }
}
