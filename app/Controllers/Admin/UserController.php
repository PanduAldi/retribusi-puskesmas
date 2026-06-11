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
        $query = $this->model->select('users.*, puskesmas.prasarana as nama_puskesmas')
                            ->join('puskesmas', 'puskesmas.id = users.id_puskesmas', 'left');

        // Filter jika admin puskesmas
        if (session()->get('role') === 'admin_puskesmas') {
            $query->where('users.id_puskesmas', session()->get('id_puskesmas'));
        }

        $users = $query->findAll();

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
        $role = $this->request->getPost('role');
        $idPuskesmas = $this->request->getPost('id_puskesmas');

        // Jika admin puskesmas, paksa id_puskesmas ke unitnya sendiri
        // dan cegah pembuatan admin_kabupaten
        if (session()->get('role') === 'admin_puskesmas') {
            $idPuskesmas = session()->get('id_puskesmas');
            if ($role === 'admin_kabupaten') {
                return redirect()->back()->withInput()->with('notif_gagal', 'Anda tidak diizinkan membuat user dengan role Admin Kabupaten.');
            }
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nama' => $this->request->getPost('nama'),
            'role' => $role,
            'id_puskesmas' => $idPuskesmas ?: null,
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

        $user = $this->model->find($id);
        if (!$user) {
            return redirect()->to('admin/users')->with('notif_gagal', 'User tidak ditemukan.');
        }

        // Cek otorisasi jika admin puskesmas
        if (session()->get('role') === 'admin_puskesmas') {
            if ($user['id_puskesmas'] != session()->get('id_puskesmas')) {
                return redirect()->to('admin/users')->with('notif_gagal', 'Anda tidak memiliki akses untuk menghapus user dari unit lain.');
            }
        }

        $this->model->delete($id);
        return redirect()->to('admin/users')->with('notif_sukses', 'User berhasil dihapus.');
    }
}
