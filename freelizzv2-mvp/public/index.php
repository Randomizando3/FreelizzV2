<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Router;

$r = new Router();

/** Público */
$r->get('/',         'App\\Controllers\\PublicController@home');
$r->get('/jobs',     'App\\Controllers\\PublicController@jobs');
$r->get('/jobs/{id}','App\\Controllers\\PublicController@jobShow');

/** Auth */
$r->get('/login',     'App\\Controllers\\AuthController@loginForm');
$r->post('/login',    'App\\Controllers\\AuthController@login');
$r->post('/logout',   'App\\Controllers\\AuthController@logout');
$r->get('/register',  'App\\Controllers\\RegisterController@form');
$r->post('/register', 'App\\Controllers\\RegisterController@submit');

/** Admin (mantém o que você já tem) */
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

/** Cliente */
$r->get('/client', 'App\\Controllers\\Client\\DashboardController@index');
$r->get('/client/jobs', 'App\\Controllers\\Client\\JobsController@index');
$r->post('/client/jobs/create', 'App\\Controllers\\Client\\JobsController@create');
$r->get('/client/jobs/{id}', 'App\\Controllers\\Client\\JobsController@show');
$r->post('/client/jobs/{id}/accept', 'App\\Controllers\\Client\\JobsController@accept');

$r->get('/client/projects', 'App\\Controllers\\Client\\ProjectsController@index');
$r->get('/client/projects/{id}', 'App\\Controllers\\Client\\ProjectsController@show');
$r->post('/client/projects/{id}/message', 'App\\Controllers\\Client\\ProjectsController@sendMessage');
$r->post('/client/projects/{id}/complete', 'App\\Controllers\\Client\\ProjectsController@markCompleted');
$r->post('/client/projects/{id}/review', 'App\\Controllers\\Client\\ProjectsController@review');
$r->post('/client/projects/{id}/dispute', 'App\\Controllers\\Client\\ProjectsController@dispute');

/** Freelancer */
$r->get('/freelancer', 'App\\Controllers\\Freelancer\\DashboardController@index');

$r->get('/freelancer/profile', 'App\\Controllers\\Freelancer\\ProfileController@form');
$r->post('/freelancer/profile', 'App\\Controllers\\Freelancer\\ProfileController@save');

$r->get('/freelancer/verification', 'App\\Controllers\\Freelancer\\VerificationController@index');
$r->post('/freelancer/verification', 'App\\Controllers\\Freelancer\\VerificationController@submit');

$r->get('/freelancer/subscription', 'App\\Controllers\\Freelancer\\SubscriptionController@index');
$r->post('/freelancer/subscription/set', 'App\\Controllers\\Freelancer\\SubscriptionController@set');

$r->get('/freelancer/jobs', 'App\\Controllers\\Freelancer\\JobsController@index');
$r->get('/freelancer/jobs/{id}', 'App\\Controllers\\Freelancer\\JobsController@show');
$r->post('/freelancer/jobs/{id}/propose', 'App\\Controllers\\Freelancer\\JobsController@propose');

$r->get('/freelancer/proposals', 'App\\Controllers\\Freelancer\\ProposalsController@index');

$r->get('/freelancer/projects', 'App\\Controllers\\Freelancer\\ProjectsController@index');
$r->get('/freelancer/projects/{id}', 'App\\Controllers\\Freelancer\\ProjectsController@show');
$r->post('/freelancer/projects/{id}/message', 'App\\Controllers\\Freelancer\\ProjectsController@sendMessage');
$r->post('/freelancer/projects/{id}/review', 'App\\Controllers\\Freelancer\\ProjectsController@review');
$r->post('/freelancer/projects/{id}/dispute', 'App\\Controllers\\Freelancer\\ProjectsController@dispute');

$r->dispatch();
