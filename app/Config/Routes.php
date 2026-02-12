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


});