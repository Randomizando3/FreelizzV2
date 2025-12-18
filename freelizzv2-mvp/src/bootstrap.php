<?php
declare(strict_types=1);

session_start();

spl_autoload_register(function (string $class): void {
  // Normaliza: remove \ inicial e colapsa \\... em \
  $class = ltrim($class, '\\');
  $class = preg_replace('/\\\\+/', '\\', $class);

  $prefix = 'App\\';
  if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;

  $relative = substr($class, strlen($prefix)); // ex: Controllers\PublicController
  $relative = ltrim($relative, '\\');
  $relative = preg_replace('/\\\\+/', '\\', $relative);

  $path = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
  if (file_exists($path)) require $path;
});

App\Helpers\Env::load(__DIR__ . '/../.env');
App\Helpers\ErrorHandler::register();
