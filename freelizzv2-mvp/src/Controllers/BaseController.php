<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Csrf;

abstract class BaseController {
  protected function view(string $tpl, array $data=[]): void {
    extract($data);
    $csrf = Csrf::token();
    $viewFile = __DIR__ . '/../../views/' . $tpl . '.php';
    $layout  = __DIR__ . '/../../views/layouts/main.php';
    if (!file_exists($viewFile)) { http_response_code(500); echo 'View nao encontrada'; return; }
    require $layout;
  }
  protected function redirect(string $to): void { header('Location: ' . $to); exit; }
}

