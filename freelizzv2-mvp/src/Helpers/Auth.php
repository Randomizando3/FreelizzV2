<?php
declare(strict_types=1);

namespace App\Helpers;

use App\DB;

final class Auth {
  public static function user(): ?array {
    $u = $_SESSION['user'] ?? null;
    return is_array($u) ? $u : null;
  }

  public static function id(): int {
    $u = self::user();
    return (int)($u['id'] ?? 0);
  }

  public static function role(): string {
    $u = self::user();
    return (string)($u['role'] ?? '');
  }

  public static function check(): bool {
    return self::id() > 0;
  }

  public static function requireLogin(): void {
    if (!self::check()) {
      header('Location: /login');
      exit;
    }
  }

  public static function requireAdmin(): void {
    self::requireLogin();
    if (self::role() !== 'admin') {
      http_response_code(403);
      echo '<h1>403</h1><p>Acesso negado</p>';
      exit;
    }
  }

  public static function isImpersonating(): bool {
    return !empty($_SESSION['_impersonator']);
  }

  public static function impersonate(int $targetUserId): void {
    self::requireAdmin();
    $pdo = DB::pdo();

    $st = $pdo->prepare("SELECT id,role,name,email,status FROM users WHERE id=? LIMIT 1");
    $st->execute([$targetUserId]);
    $u = $st->fetch();

    if (!$u || $u['status'] !== 'active') {
      Flash::set('error', 'Usuário inválido para impersonar.');
      header('Location: /admin/users');
      exit;
    }

    $_SESSION['_impersonator'] = self::user(); // guarda admin
    $_SESSION['user'] = ['id'=>$u['id'],'role'=>$u['role'],'name'=>$u['name'],'email'=>$u['email']];
  }

  public static function stopImpersonate(): void {
    self::requireLogin();
    if (!self::isImpersonating()) {
      header('Location: /admin');
      exit;
    }
    $_SESSION['user'] = $_SESSION['_impersonator'];
    unset($_SESSION['_impersonator']);
  }
}
