<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================================================
// DEFAULT
// =====================================================

$routes->get('/', function () {
    return redirect()->to('/login');
});

// =====================================================
// AUTH (PUBLIC)
// =====================================================

$routes->get('login', 'Auth::login');
$routes->post('login/process', 'Auth::processLogin');
$routes->get('logout', 'Auth::logout');

// =====================================================
// PROTECTED AREA (AUTH FILTER)
// =====================================================

$routes->group('', ['filter' => 'auth'], function ($routes) {

    $routes->get('dashboard', 'Dashboard::index');

    // SUPERADMIN ONLY (0)
    $routes->group('', ['filter' => 'role:0'], function ($routes) {
        $routes->group('users', function ($routes) {
            $routes->get('/', 'User::index');
            $routes->post('save', 'User::save');
            $routes->post('delete', 'User::delete');
        });
    });

    // SUPERADMIN + ADMIN (0,1)
    $routes->group('', ['filter' => 'role:0,1'], function ($routes) {

        $routes->group('merk', function ($routes) {
            $routes->get('/', 'Merk::index');
            $routes->post('save', 'Merk::save');
            $routes->post('delete', 'Merk::delete');
        });

        $routes->group('jenis', function ($routes) {
            $routes->get('/', 'Jenis::index');
            $routes->post('save', 'Jenis::save');
            $routes->post('delete', 'Jenis::delete');
        });

        $routes->group('lokasi', function ($routes) {
            $routes->get('/', 'Lokasi::index');
            $routes->post('save', 'Lokasi::save');
            $routes->post('delete', 'Lokasi::delete');
        });

        $routes->group('vendors', function ($routes) {
            $routes->get('/', 'Vendors::index');
            $routes->post('save', 'Vendors::save');
            $routes->post('delete', 'Vendors::delete');
        });

        $routes->group('aset', function ($routes) {
            $routes->get('/', 'Aset::index');
            $routes->post('save', 'Aset::save');
            $routes->post('delete', 'Aset::delete');
        });
    });

    // SEMUA LOGIN (0,1,2)
    $routes->group('', ['filter' => 'role:0,1,2'], function ($routes) {

        $routes->group('service', function ($routes) {
            $routes->get('/', 'Service::index');
            $routes->post('save', 'Service::save');
            $routes->post('delete', 'Service::delete');
        });

        $routes->group('mutasi-aset', function ($routes) {
            $routes->get('/', 'MutasiAset::index');
            $routes->post('save', 'MutasiAset::save');
            $routes->post('delete', 'MutasiAset::delete');
        });
    });
});