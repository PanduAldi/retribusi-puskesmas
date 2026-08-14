<?php

namespace App\Controllers\H2h;

use App\Controllers\BaseController;

class H2hTestController extends BaseController
{
    public function index()
    {
        return view('eretribusi/h2h_simulator');
    }
}
