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

        $role = session()->get('role');
        $idPuskesmas = session()->get('id_puskesmas');

        if ($role === 'admin_puskesmas') {
            $total_puskesmas = 1;
            $total_transaksi = $transaksiModel->where('id_puskesmas', $idPuskesmas)->countAllResults();
            $total_pendapatan = $transaksiModel->db->table('transaksi_item')
                ->join('transaksi_retribusi', 'transaksi_retribusi.id = transaksi_item.id_transaksi')
                ->where('transaksi_retribusi.id_puskesmas', $idPuskesmas)
                ->where('transaksi_retribusi.status', 1)
                ->selectSum('amount')
                ->get()->getRowArray()['amount'] ?? 0;
            $puskesmas_list = $puskesmasModel->where('id', $idPuskesmas)->findAll();
        } else {
            $total_puskesmas = $puskesmasModel->countAllResults();
            $total_transaksi = $transaksiModel->countAllResults();
            $total_pendapatan = $transaksiModel->db->table('transaksi_item')
                ->join('transaksi_retribusi', 'transaksi_retribusi.id = transaksi_item.id_transaksi')
                ->where('transaksi_retribusi.status', 1)
                ->selectSum('amount')
                ->get()->getRowArray()['amount'] ?? 0;
            $puskesmas_list = $puskesmasModel->findAll();
        }

        // 5 Kategori Terbanyak
        $db = \Config\Database::connect();
        $builder = $db->table('transaksi_item');
        $builder->select('jenis_retribusi.kategori, SUM(transaksi_item.volume) as total_volume, SUM(transaksi_item.amount) as total_amount');
        $builder->join('jenis_retribusi', 'jenis_retribusi.id = transaksi_item.id_jenis');
        $builder->join('transaksi_retribusi', 'transaksi_retribusi.id = transaksi_item.id_transaksi');

        if ($role === 'admin_puskesmas') {
            $builder->where('transaksi_retribusi.id_puskesmas', $idPuskesmas);
        }

        $builder->where('transaksi_retribusi.status', 1); // Hanya yang sudah lunas
        $builder->groupBy('jenis_retribusi.kategori');
        $builder->orderBy('total_volume', 'DESC');
        $builder->limit(5);
        $top_kategori = $builder->get()->getResultArray();

        $data = [
            'total_puskesmas' => $total_puskesmas,
            'total_transaksi' => $total_transaksi,
            'total_pendapatan' => $total_pendapatan,
            'puskesmas_list' => $puskesmas_list,
            'top_kategori' => $top_kategori,
        ];

        return view('admin/dashboard', $data);
    }
}
