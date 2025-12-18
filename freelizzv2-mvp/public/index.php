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


// Admin
$r->get('/admin', 'App\\Controllers\\Admin\\AdminController@dashboard');

$r->get('/admin/users', 'App\\Controllers\\Admin\\UsersController@index');
$r->get('/admin/users/create', 'App\\Controllers\\Admin\\UsersController@createForm');
$r->post('/admin/users/create', 'App\\Controllers\\Admin\\UsersController@create');
$r->get('/admin/users/{id}/edit', 'App\\Controllers\\Admin\\UsersController@editForm');
$r->post('/admin/users/{id}/edit', 'App\\Controllers\\Admin\\UsersController@update');
$r->post('/admin/users/{id}/block', 'App\\Controllers\\Admin\\UsersController@block');
$r->post('/admin/users/{id}/unblock', 'App\\Controllers\\Admin\\UsersController@unblock');
$r->post('/admin/users/{id}/impersonate', 'App\\Controllers\\Admin\\UsersController@impersonate');
$r->post('/admin/impersonate/stop', 'App\\Controllers\\Admin\\UsersController@stopImpersonate');

$r->get('/admin/categories', 'App\\Controllers\\Admin\\CategoriesController@index');
$r->post('/admin/categories/create', 'App\\Controllers\\Admin\\CategoriesController@create');
$r->post('/admin/categories/{id}/edit', 'App\\Controllers\\Admin\\CategoriesController@update');
$r->post('/admin/categories/{id}/delete', 'App\\Controllers\\Admin\\CategoriesController@delete');

$r->get('/admin/jobs', 'App\\Controllers\\Admin\\JobsController@index');
$r->post('/admin/jobs/create', 'App\\Controllers\\Admin\\JobsController@create');
$r->get('/admin/jobs/{id}', 'App\\Controllers\\Admin\\JobsController@show');
$r->post('/admin/jobs/{id}/status', 'App\\Controllers\\Admin\\JobsController@updateStatus');

$r->get('/admin/verifications', 'App\\Controllers\\Admin\\VerificationsController@index');
$r->get('/admin/verifications/{id}', 'App\\Controllers\\Admin\\VerificationsController@show');
$r->post('/admin/verifications/{id}/approve', 'App\\Controllers\\Admin\\VerificationsController@approve');
$r->post('/admin/verifications/{id}/reject', 'App\\Controllers\\Admin\\VerificationsController@reject');

$r->get('/admin/settings', 'App\\Controllers\\Admin\\SettingsController@index');
$r->post('/admin/settings/save', 'App\\Controllers\\Admin\\SettingsController@save');
$r->post('/admin/settings/plan/{code}', 'App\\Controllers\\Admin\\SettingsController@savePlan');



$r->dispatch();
