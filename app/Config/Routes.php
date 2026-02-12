<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('users', function ($routes) {
    $routes->get('/', 'User::index');
    $routes->get('create', 'User::create');
    $routes->post('store', 'User::store');
    $routes->get('edit/(:num)', 'User::edit/$1');
    $routes->post('update/(:num)', 'User::update/$1');
    $routes->get('delete/(:num)', 'User::delete/$1');
});

// ===== MERK =====
$routes->group('merk', function ($routes) {
    $routes->get('/', 'Merk::index');
    $routes->get('create', 'Merk::create');
    $routes->post('store', 'Merk::store');
    $routes->get('edit/(:num)', 'Merk::edit/$1');
    $routes->post('update/(:num)', 'Merk::update/$1');
    $routes->get('delete/(:num)', 'Merk::delete/$1');
});

// ===== LOKASI =====
$routes->group('lokasi', function ($routes) {
    $routes->get('/', 'Lokasi::index');
    $routes->get('create', 'Lokasi::create');
    $routes->post('store', 'Lokasi::store');
    $routes->get('edit/(:num)', 'Lokasi::edit/$1');
    $routes->post('update/(:num)', 'Lokasi::update/$1');
    $routes->get('delete/(:num)', 'Lokasi::delete/$1');
});
