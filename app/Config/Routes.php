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

    // DASHBOARD
    $routes->get('dashboard', 'Dashboard::index');

    // =================================================
    // USERS
    // =================================================
    $routes->group('users', function ($routes) {
        $routes->get('/', 'User::index');
        $routes->post('save', 'User::save');
        $routes->post('delete', 'User::delete');
    });


    // =================================================
    // MERK
    // =================================================
    $routes->group('merk', function ($routes) {
        $routes->get('/', 'Merk::index');
        $routes->post('save', 'Merk::save');
        $routes->post('delete', 'Merk::delete');
    });


    // =================================================
    // LOKASI
    // =================================================
    $routes->group('lokasi', function ($routes) {
        $routes->get('/', 'Lokasi::index');
        $routes->post('save', 'Lokasi::save');
        $routes->post('delete', 'Lokasi::delete');
    });

    $routes->group('jenis', ['filter' => 'auth'], function ($routes) {
        $routes->get('/', 'Jenis::index');
        $routes->post('save', 'Jenis::save');
        $routes->post('delete', 'Jenis::delete');
    });

    $routes->group('vendors', ['filter' => 'auth'], function ($routes) {
        $routes->get('/', 'Vendors::index');
        $routes->post('save', 'Vendors::save');
        $routes->post('delete', 'Vendors::delete');
    });
});
