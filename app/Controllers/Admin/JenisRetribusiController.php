<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JenisRetribusiModel;

class JenisRetribusiController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new JenisRetribusiModel();
    }

    public function index()
    {
        return view('admin/jenis-retribusi/index', [
            'title' => 'Master Jenis Retribusi',
            'jenis' => $this->model->findAll()
        ]);
    }

    public function store()
    {
        $this->model->insert([
            'kategori' => $this->request->getPost('kategori'),
            'jenis'    => $this->request->getPost('jenis'),
            'tarif'    => $this->request->getPost('tarif')
        ]);
        return redirect()->to('admin/jenis-retribusi')->with('notif_sukses', 'Jenis retribusi berhasil ditambah.');
    }

    public function update($id)
    {
        $this->model->update($id, [
            'kategori' => $this->request->getPost('kategori'),
            'jenis'    => $this->request->getPost('jenis'),
            'tarif'    => $this->request->getPost('tarif')
        ]);
        return redirect()->to('admin/jenis-retribusi')->with('notif_sukses', 'Jenis retribusi berhasil diupdate.');
    }

    public function delete($id)
    {
        $itemModel = new \App\Models\TransaksiItemModel();
        $isUsed = $itemModel->where('id_jenis', $id)->countAllResults() > 0;

        if ($isUsed) {
            return redirect()->to('admin/jenis-retribusi')->with('notif_gagal', 'Jenis retribusi tidak bisa dihapus karena sudah digunakan dalam transaksi.');
        }

        $this->model->delete($id);
        return redirect()->to('admin/jenis-retribusi')->with('notif_sukses', 'Jenis retribusi berhasil dihapus.');
    }
}