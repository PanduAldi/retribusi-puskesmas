<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PuskesmasModel;

class PuskesmasController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new PuskesmasModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Puskesmas',
            'puskesmas' => $this->model->findAll()
        ];
        return view('admin/puskesmas/index', $data);
    }

    public function create()
    {
        return view('admin/puskesmas/form', ['title' => 'Tambah Puskesmas']);
    }

    public function store()
    {
        $data = [
            'prasarana' => $this->request->getPost('prasarana'),
            'kode_retribusi' => $this->request->getPost('kode_retribusi')
        ];

        $this->model->insert($data);
        return redirect()->to('admin/puskesmas')->with('notif_sukses', 'Data puskesmas berhasil ditambah.');
    }

    public function edit($id)
    {
        $pkm = $this->model->find($id);
        if (!$pkm) return redirect()->to('admin/puskesmas')->with('notif_gagal', 'Data tidak ditemukan.');

        return view('admin/puskesmas/form', [
            'title' => 'Edit Puskesmas',
            'puskesmas' => $pkm
        ]);
    }

    public function update($id)
    {
        $data = [
            'prasarana' => $this->request->getPost('prasarana'),
            'kode_retribusi' => $this->request->getPost('kode_retribusi')
        ];

        $this->model->update($id, $data);
        return redirect()->to('admin/puskesmas')->with('notif_sukses', 'Data puskesmas berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect()->to('admin/puskesmas')->with('notif_sukses', 'Data puskesmas berhasil dihapus.');
    }
}
