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
        $this->model->insert(['jenis' => $this->request->getPost('jenis')]);
        return redirect()->to('admin/jenis-retribusi')->with('notif_sukses', 'Jenis retribusi berhasil ditambah.');
    }

    public function update($id)
    {
        $this->model->update($id, ['jenis' => $this->request->getPost('jenis')]);
        return redirect()->to('admin/jenis-retribusi')->with('notif_sukses', 'Jenis retribusi berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect()->to('admin/jenis-retribusi')->with('notif_sukses', 'Jenis retribusi berhasil dihapus.');
    }
}
