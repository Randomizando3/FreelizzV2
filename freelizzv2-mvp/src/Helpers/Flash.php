<?php
declare(strict_types=1);

namespace App\Helpers;

final class Flash {
  public static function set(string $type, string $msg): void {
    $_SESSION['_flash'] = ['type' => $type, 'msg' => $msg];
  }

  public static function get(): ?array {
    if (empty($_SESSION['_flash'])) return null;
    $f = $_SESSION['_flash'];
    unset($_SESSION['_flash']);
    return is_array($f) ? $f : null;
  }
}
