<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('setup', 'Auth::setup');
$routes->post('setup', 'Auth::createAccount');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');
$routes->get('media/(:segment)/(:segment)', 'Media::show/$1/$2');

$routes->group('', ['filter' => 'auth'], static function ($routes): void {
    $routes->get('/', 'Pos::index');
    $routes->get('pos', 'Pos::index');
    $routes->post('pos/charge', 'Pos::charge');
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('products', 'Products::index');
    $routes->get('products/new', 'Products::newProduct');
    $routes->post('products/create', 'Products::create');
    $routes->get('products/(:num)/edit', 'Products::edit/$1');
    $routes->post('products/(:num)/update', 'Products::update/$1');
    $routes->post('products/(:num)/delete', 'Products::delete/$1');
    $routes->post('products/(:num)/restock', 'Products::restock/$1');
    $routes->get('customers', 'Customers::index');
    $routes->get('customers/new', 'Customers::newCustomer');
    $routes->post('customers/create', 'Customers::create');
    $routes->get('customers/(:num)', 'Customers::show/$1');
    $routes->get('customers/(:num)/edit', 'Customers::edit/$1');
    $routes->post('customers/(:num)/update', 'Customers::update/$1');
    $routes->post('customers/(:num)/delete', 'Customers::delete/$1');
    $routes->get('staff', 'Staff::index');
    $routes->get('staff/new', 'Staff::newStaff');
    $routes->post('staff/create', 'Staff::create');
    $routes->get('staff/(:num)/edit', 'Staff::edit/$1');
    $routes->post('staff/(:num)/update', 'Staff::update/$1');
    $routes->post('staff/(:num)/delete', 'Staff::delete/$1');
    $routes->get('account', 'Staff::account');
    $routes->get('sales', 'Sales::index');
    $routes->get('sales/new', 'Sales::newSale');
    $routes->post('sales/create', 'Sales::create');
    $routes->get('sales/receipt/(:num)', 'Sales::receipt/$1');
    $routes->get('sales/export', 'Sales::export');
    $routes->get('about', 'About::index');
    $routes->post('logout', 'Auth::logout');
});
