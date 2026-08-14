<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Auth Routes
$routes->get('login', 'Auth\AuthController::login');
$routes->post('login', 'Auth\AuthController::attemptLogin');
$routes->get('logout', 'Auth\AuthController::logout');

// H2H Routes - Host to Host dengan Bank Jateng
$routes->group('h2h', ['namespace' => 'App\Controllers\H2h'], function($routes) {
    $routes->get('auth', 'H2hController::auth');
    $routes->post('inquiry', 'H2hController::inquiry');
    $routes->post('payment', 'H2hController::payment');
});

// Simulator & Testing H2H
$routes->group('eretribusi/h2h-test', ['namespace' => 'App\Controllers\H2h', 'filter' => 'auth'], function($routes) {
    $routes->get('', 'H2hTestController::index');
});

// Admin Kabupaten Routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:admin_kabupaten,admin_puskesmas'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    // Puskesmas CRUD
    $routes->get('puskesmas', 'PuskesmasController::index');
    $routes->get('puskesmas/new', 'PuskesmasController::create');
    $routes->post('puskesmas/store', 'PuskesmasController::store');
    $routes->get('puskesmas/edit/(:num)', 'PuskesmasController::edit/$1');
    $routes->post('puskesmas/update/(:num)', 'PuskesmasController::update/$1');
    $routes->get('puskesmas/delete/(:num)', 'PuskesmasController::delete/$1');

    // Puskesmas Jenis Mapping
    $routes->get('puskesmas/jenis', 'PuskesmasJenisController::index');
    $routes->post('puskesmas/jenis/save', 'PuskesmasJenisController::save');

    // Master Layanan & Tarif (sebelumnya Jenis Retribusi)
    $routes->get('jenis-retribusi', 'JenisRetribusiController::index');
    $routes->post('jenis-retribusi/store', 'JenisRetribusiController::store');
    $routes->post('jenis-retribusi/update/(:num)', 'JenisRetribusiController::update/$1');
    $routes->get('jenis-retribusi/delete/(:num)', 'JenisRetribusiController::delete/$1');

    // Users CRUD
    $routes->get('users', 'UserController::index');
    $routes->get('users/new', 'UserController::create');
    $routes->post('users/store', 'UserController::store');
    $routes->get('users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('users/update/(:num)', 'UserController::update/$1');
    $routes->get('users/delete/(:num)', 'UserController::delete/$1');
});

// E-Retribusi Routes
$routes->group('eretribusi', ['namespace' => 'App\Controllers\Eretribusi', 'filter' => 'auth'], function($routes) {
    // Pasien Auto-fill API
    $routes->get('pasien/cari/(:any)', 'PasienApiController::getPasien/$1');

    // Billing routes
    $routes->get('konfirmasi/(:segment)', 'BillingController::konfirmasi/$1');
    $routes->post('generate', 'BillingController::generate');
    $routes->get('qris/(:segment)', 'BillingController::qris/$1');
    $routes->get('billing/cek-status', 'BillingController::cekStatus');
    $routes->post('billing/cek-status', 'BillingController::prosesCekStatus');

    // Transaksi routes
    $routes->get('transaksi', 'TransaksiController::index');
    $routes->get('transaksi/new', 'TransaksiController::create');
    $routes->post('transaksi/store', 'TransaksiController::store');
    $routes->get('transaksi/laporan', 'TransaksiController::laporan');
});
