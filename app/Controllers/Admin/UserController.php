<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PuskesmasModel;

class UserController extends BaseController
{
    protected $model;
    protected $pkmModel;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->pkmModel = new PuskesmasModel();
    }

    public function index()
    {
        $users = $this->model->select('users.*, puskesmas.prasarana as nama_puskesmas')
                            ->join('puskesmas', 'puskesmas.id = users.id_puskesmas', 'left')
                            ->findAll();

        return view('admin/users/index', [
            'title' => 'Manajemen Pengguna',
            'users' => $users
        ]);
    }

    public function create()
    {
        return view('admin/users/form', [
            'title' => 'Tambah User Baru',
            'puskesmas' => $this->pkmModel->findAll()
        ]);
    }

    public function store()
    {
        $data = [
            'username' => $this->request->getPost('username'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nama' => $this->request->getPost('nama'),
            'role' => $this->request->getPost('role'),
            'id_puskesmas' => $this->request->getPost('id_puskesmas') ?: null,
            'is_active' => 1
        ];

        $this->model->insert($data);
        return redirect()->to('admin/users')->with('notif_sukses', 'User berhasil ditambahkan.');
    }

    public function delete($id)
    {
        if ($id == session()->get('user_id')) {
            return redirect()->to('admin/users')->with('notif_gagal', 'Tidak dapat menghapus diri sendiri.');
        }
        $this->model->delete($id);
        return redirect()->to('admin/users')->with('notif_sukses', 'User berhasil dihapus.');
    }
}
