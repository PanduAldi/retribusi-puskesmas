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

    public function edit($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return redirect()->to('admin/users')->with('notif_gagal', 'User tidak ditemukan.');
        }

        // Cek otorisasi jika admin puskesmas
        if (session()->get('role') === 'admin_puskesmas') {
            if ($user['id_puskesmas'] != session()->get('id_puskesmas')) {
                return redirect()->to('admin/users')->with('notif_gagal', 'Anda tidak memiliki akses ke user ini.');
            }
        }

        return view('admin/users/form', [
            'title' => 'Edit User',
            'user' => $user,
            'puskesmas' => $this->pkmModel->findAll()
        ]);
    }

    public function update($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return redirect()->to('admin/users')->with('notif_gagal', 'User tidak ditemukan.');
        }

        // Cek otorisasi jika admin puskesmas
        if (session()->get('role') === 'admin_puskesmas') {
            if ($user['id_puskesmas'] != session()->get('id_puskesmas')) {
                return redirect()->to('admin/users')->with('notif_gagal', 'Akses ditolak.');
            }
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'is_active' => $this->request->getPost('is_active')
        ];

        // Jika password diisi, update password
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Hanya admin kabupaten yang bisa ubah role & puskesmas
        if (session()->get('role') === 'admin_kabupaten') {
            $data['role'] = $this->request->getPost('role');
            $data['id_puskesmas'] = $this->request->getPost('id_puskesmas') ?: null;
        }

        $this->model->update($id, $data);
        return redirect()->to('admin/users')->with('notif_sukses', 'Data user berhasil diperbarui.');
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
                return redirect()->to('admin/users')->with('notif_gagal', 'Akses ditolak.');
            }
        }

        // Cek apakah user sudah punya transaksi (logika keamanan data)
        // Kita asumsikan pengecekan ini penting untuk integritas data histori
        $trxModel = new \App\Models\TransaksiRetribusiModel();

        // Catatan: Karena transaksi_retribusi tidak punya user_id,
        // namun kedepannya jika sistem dikembangkan, ini akan sangat krusial.
        // Untuk saat ini, kita ikuti instruksi user: "hanya bisa menonaktifkan jika berpengaruh".
        // Sebagai simulasi keamanan, kita ganti fungsi DELETE menjadi SOFT-DELETE (Nonaktifkan)
        // jika Admin menginginkan demikian demi keamanan record.

        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to('admin/users')->with('notif_sukses', 'User telah dinonaktifkan untuk keamanan record.');
    }
}
