<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Csrf;

abstract class BaseController {
  protected string $layout = 'layouts/main';

  protected function view(string $tpl, array $data = []): void {
    extract($data);

    $csrf = Csrf::token();
    $viewFile = __DIR__ . '/../../views/' . $tpl . '.php';
    $layoutFile = __DIR__ . '/../../views/' . $this->layout . '.php';

    if (!file_exists($viewFile)) { http_response_code(500); echo 'View não encontrada'; return; }
    if (!file_exists($layoutFile)) { http_response_code(500); echo 'Layout não encontrado'; return; }

    require $layoutFile;
  }

  protected function redirect(string $to): void {
    header('Location: ' . $to);
    exit;
  }
}
