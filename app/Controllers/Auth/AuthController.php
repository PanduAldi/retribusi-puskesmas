<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $throttler = service('throttler');

        // Use IP address as the key for throttling
        $key = $this->request->getIPAddress();

        // Read limits from .env or use defaults (5 attempts per 60 seconds)
        $threshold = (int) env('throttler.threshold', 5);
        $interval  = (int) env('throttler.interval', 60);

        if ($throttler->check(md5($key), $threshold, $interval) === false) {
            return redirect()->back()->with('notif_gagal', 'Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa menit.');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->getByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['is_active'] == 0) {
                return redirect()->back()->with('notif_gagal', 'Akun Anda tidak aktif.');
            }

            $sessionData = [
                'user_id'      => $user['id'],
                'username'     => $user['username'],
                'nama'         => $user['nama'],
                'role'         => $user['role'],
                'id_puskesmas' => $user['id_puskesmas'],
                'is_logged_in' => true,
            ];

            session()->set($sessionData);

            // Update last login
            $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

            // Redirect based on role
            if (in_array($user['role'], ['admin_kabupaten', 'admin_puskesmas'])) {
                return redirect()->to('/admin/dashboard');
            }
            return redirect()->to('/');
        }

        return redirect()->back()->with('notif_gagal', 'Username atau password salah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
