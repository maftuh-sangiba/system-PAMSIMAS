<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('login', function ($routes) {
    $routes->get('/', 'Login::index');
    $routes->post('auth', 'Login::auth');
});

$routes->get('logout', 'Login::logout');

$routes->group('/', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    $routes->group('pelanggan', function ($routes) {
        $routes->get('/', 'Pelanggan::index');
        $routes->post('store', 'Pelanggan::store');
        $routes->get('delete/(:num)', 'Pelanggan::delete/$1');
        $routes->get('getData/(:num)', 'Pelanggan::getData/$1');
    });

    $routes->group('meteran', function ($routes) {
        $routes->get('/', 'Meteran::index');
        $routes->post('store', 'Meteran::store');
        $routes->get('delete/(:num)', 'Meteran::delete/$1');
        $routes->get('getData/(:num)', 'Meteran::getData/$1');
        $routes->get('getPelanggan', 'Meteran::getPelanggan');
    });

    $routes->group('penggunaan', function ($routes) {
        $routes->get('/', 'Penggunaan::index');
        $routes->get('insert', 'Penggunaan::insert');
        $routes->post('store', 'Penggunaan::store');
        $routes->post('getAllMeteran', 'Penggunaan::getAllMeteran');
        $routes->post('getDataFiltered', 'Penggunaan::getDataFiltered');
    });

    $routes->group('pembayaran', function ($routes) {
        $routes->get('/', 'Pembayaran::index');
        $routes->post('getDataFiltered', 'Pembayaran::getDataFiltered');
        $routes->post('bayar', 'Pembayaran::bayar');
    });

    $routes->get('saldo', 'Saldo::index');
});


$routes->group('api', function ($routes) {
    $routes->post('login', 'Api\Login::login');
    $routes->post('checkToken', 'Api\Login::checkToken');
    
    $routes->group('pembayaran', ['filter' => 'authApi'], function ($routes) {
        $routes->post('check', 'Api\Pembayaran::check');
        $routes->post('pay', 'Api\Pembayaran::pay');
    });

    $routes->group('penggunaan', ['filter' => 'authApi'], function ($routes) {
        $routes->post('store', 'Api\Penggunaan::store');
    });
});
