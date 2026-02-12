<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('users', function($routes) {
    $routes->get('/', 'UserController::index');
    $routes->get('create', 'UserController::create');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->get('delete/(:num)', 'UserController::delete/$1');
});

// ===== MERK =====
$routes->group('merk', function($routes) {
    $routes->get('/', 'Merk::index');
    $routes->get('create', 'Merk::create');
    $routes->post('store', 'Merk::store');
    $routes->get('edit/(:num)', 'Merk::edit/$1');
    $routes->post('update/(:num)', 'Merk::update/$1');
    $routes->get('delete/(:num)', 'Merk::delete/$1');
});

// ===== LOKASI =====
$routes->group('lokasi', function($routes) {
    $routes->get('/', 'Lokasi::index');
    $routes->get('create', 'Lokasi::create');
    $routes->post('store', 'Lokasi::store');
    $routes->get('edit/(:num)', 'Lokasi::edit/$1');
    $routes->post('update/(:num)', 'Lokasi::update/$1');
    $routes->get('delete/(:num)', 'Lokasi::delete/$1');
});