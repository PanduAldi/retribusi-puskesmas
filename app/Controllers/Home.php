<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        if (session()->get('role') === 'admin_kabupaten') {
            return redirect()->to('/admin/dashboard');
        }

        return view('dashboard/index');
    }
}
