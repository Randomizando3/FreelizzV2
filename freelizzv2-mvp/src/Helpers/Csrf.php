<?php
declare(strict_types=1);

namespace App\Helpers;

final class Csrf {
  public static function token(): string {
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    return (string) $_SESSION['_csrf'];
  }
  public static function check(): void {
    $sent = (string) ($_POST['_csrf'] ?? '');
    $ok = isset($_SESSION['_csrf']) && hash_equals((string)$_SESSION['_csrf'], $sent);
    if (!$ok) {
      http_response_code(419);
      echo '<h1>419</h1><p>CSRF inválido</p>';
      exit;
    }
  }
}

