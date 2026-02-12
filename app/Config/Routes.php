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
        $routes->get('create', 'User::create');
        $routes->post('store', 'User::store');
        $routes->get('edit/(:num)', 'User::edit/$1');
        $routes->post('update/(:num)', 'User::update/$1');
        $routes->get('delete/(:num)', 'User::delete/$1');
    });

    // =================================================
    // MERK
    // =================================================
    $routes->group('merk', function ($routes) {
        $routes->get('/', 'Merk::index');
        $routes->get('create', 'Merk::create');
        $routes->post('store', 'Merk::store');
        $routes->get('edit/(:num)', 'Merk::edit/$1');
        $routes->post('update/(:num)', 'Merk::update/$1');
        $routes->get('delete/(:num)', 'Merk::delete/$1');
    });

    // =================================================
    // LOKASI
    // =================================================
    $routes->group('lokasi', function ($routes) {
        $routes->get('/', 'Lokasi::index');
        $routes->get('create', 'Lokasi::create');
        $routes->post('store', 'Lokasi::store');
        $routes->get('edit/(:num)', 'Lokasi::edit/$1');
        $routes->post('update/(:num)', 'Lokasi::update/$1');
        $routes->get('delete/(:num)', 'Lokasi::delete/$1');
    });

});