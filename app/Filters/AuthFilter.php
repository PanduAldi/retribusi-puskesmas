<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login')->with('notif_gagal', 'Anda harus login terlebih dahulu.');
        }

        if (!empty($arguments)) {
            $userRole = session()->get('role');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/')->with('notif_gagal', 'Anda tidak memiliki hak akses ke halaman tersebut.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
