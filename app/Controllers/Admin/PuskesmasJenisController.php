<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PuskesmasModel;
use App\Models\JenisRetribusiModel;
use App\Models\PuskesmasJenisModel;

class PuskesmasJenisController extends BaseController
{
    protected $puskesmasModel;
    protected $jenisModel;
    protected $pivotModel;

    public function __construct()
    {
        $this->puskesmasModel = new PuskesmasModel();
        $this->jenisModel = new JenisRetribusiModel();
        $this->pivotModel = new PuskesmasJenisModel();
    }

    /**
     * Display mapping grid
     */
    public function index()
    {
        $allPuskesmas = $this->puskesmasModel->findAll();
        $allJenis = $this->jenisModel->findAll();

        // Build existing mapping matrix
        $mappings = [];
        $rawMappings = $this->pivotModel->findAll();
        foreach ($rawMappings as $row) {
            $mappings[$row['id_puskesmas']][] = $row['id_jenis'];
        }

        return view('admin/puskesmas/jenis', [
            'title'        => 'Mapping Layanan per Puskesmas',
            'allPuskesmas' => $allPuskesmas,
            'allJenis'     => $allJenis,
            'mappings'     => $mappings
        ]);
    }

    /**
     * Save mappings for a specific puskesmas
     */
    public function save()
    {
        $idPuskesmas = $this->request->getPost('id_puskesmas');
        $idJenisList = $this->request->getPost('id_jenis') ?? [];

        if (!$idPuskesmas) {
            return redirect()->back()->with('notif_gagal', 'ID Puskesmas tidak valid.');
        }

        if ($this->pivotModel->syncMappings((int)$idPuskesmas, $idJenisList)) {
            return redirect()->back()->with('notif_sukses', 'Mapping layanan berhasil diperbarui.');
        } else {
            return redirect()->back()->with('notif_gagal', 'Gagal memperbarui mapping.');
        }
    }
}
