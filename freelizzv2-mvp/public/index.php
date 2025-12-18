<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Router;

$r = new Router();

$r->get('/',        'App\\Controllers\\PublicController@home');
$r->get('/jobs',    'App\\Controllers\\PublicController@jobs');
$r->get('/jobs/{id}','App\\Controllers\\PublicController@jobShow');

$r->get('/login',   'App\\Controllers\\AuthController@loginForm');
$r->post('/login',  'App\\Controllers\\AuthController@login');
$r->post('/logout', 'App\\Controllers\\AuthController@logout');

$r->dispatch();
