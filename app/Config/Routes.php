<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Auth Routes
$routes->get('login', 'Auth\AuthController::login');
$routes->post('login', 'Auth\AuthController::attemptLogin');
$routes->get('logout', 'Auth\AuthController::logout');

// Admin Kabupaten Routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:admin_kabupaten'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    // Puskesmas CRUD
    $routes->get('puskesmas', 'PuskesmasController::index');
    $routes->get('puskesmas/new', 'PuskesmasController::create');
    $routes->post('puskesmas/store', 'PuskesmasController::store');
    $routes->get('puskesmas/edit/(:num)', 'PuskesmasController::edit/$1');
    $routes->post('puskesmas/update/(:num)', 'PuskesmasController::update/$1');
    $routes->get('puskesmas/delete/(:num)', 'PuskesmasController::delete/$1');

    // Jenis Retribusi CRUD
    $routes->get('jenis-retribusi', 'JenisRetribusiController::index');
    $routes->post('jenis-retribusi/store', 'JenisRetribusiController::store');
    $routes->post('jenis-retribusi/update/(:num)', 'JenisRetribusiController::update/$1');
    $routes->get('jenis-retribusi/delete/(:num)', 'JenisRetribusiController::delete/$1');

    // Users CRUD
    $routes->get('users', 'UserController::index');
    $routes->get('users/new', 'UserController::create');
    $routes->post('users/store', 'UserController::store');
    $routes->get('users/delete/(:num)', 'UserController::delete/$1');

    // Tarif CRUD
    $routes->get('tarif', 'TarifController::index');
    $routes->post('tarif/store', 'TarifController::store');
    $routes->get('tarif/delete/(:num)', 'TarifController::delete/$1');
});

// E-Retribusi Routes
$routes->group('eretribusi', ['namespace' => 'App\Controllers\Eretribusi', 'filter' => 'auth'], function($routes) {
    // Billing routes
    $routes->get('konfirmasi/(:segment)', 'BillingController::konfirmasi/$1');
    $routes->post('generate', 'BillingController::generate');
    $routes->get('qris/(:segment)', 'BillingController::qris/$1');

    // Transaksi routes
    $routes->get('transaksi', 'TransaksiController::index');
    $routes->get('transaksi/new', 'TransaksiController::create');
    $routes->post('transaksi/store', 'TransaksiController::store');
});
