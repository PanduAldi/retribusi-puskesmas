<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TarifRetribusiModel;
use App\Models\PuskesmasModel;
use App\Models\JenisRetribusiModel;

class TarifController extends BaseController
{
    protected $model;
    protected $puskesmasModel;
    protected $jenisModel;

    public function __construct()
    {
        $this->model = new TarifRetribusiModel();
        $this->puskesmasModel = new PuskesmasModel();
        $this->jenisModel = new JenisRetribusiModel();
    }

    public function index()
    {
        $tarif = $this->model->select('tarif_retribusi.*, puskesmas.prasarana as nama_puskesmas, jenis_retribusi.jenis as nama_jenis')
                        ->join('puskesmas', 'puskesmas.id = tarif_retribusi.id_puskesmas')
                        ->join('jenis_retribusi', 'jenis_retribusi.id = tarif_retribusi.id_jenis')
                        ->findAll();

        return view('admin/tarif/index', [
            'title' => 'Manajemen Tarif',
            'tarif' => $tarif,
            'puskesmas' => $this->puskesmasModel->findAll(),
            'jenis' => $this->jenisModel->findAll()
        ]);
    }

    public function store()
    {
        $data = [
            'id_puskesmas' => $this->request->getPost('id_puskesmas'),
            'id_jenis' => $this->request->getPost('id_jenis'),
            'tarif' => $this->request->getPost('tarif')
        ];

        $this->model->insert($data);
        return redirect()->to('admin/tarif')->with('notif_sukses', 'Tarif berhasil disimpan.');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect()->to('admin/tarif')->with('notif_sukses', 'Tarif berhasil dihapus.');
    }
}
