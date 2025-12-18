<?php
declare(strict_types=1);

namespace App\Helpers;

use App\DB;

final class Settings {
  public static function all(): array {
    $pdo = DB::pdo();
    $rows = $pdo->query("SELECT `key`, `value` FROM settings")->fetchAll();
    $out = [];
    foreach ($rows as $r) $out[(string)$r['key']] = (string)$r['value'];
    return $out;
  }

  public static function get(string $key, $default=null) {
    $pdo = DB::pdo();
    $st = $pdo->prepare("SELECT `value` FROM settings WHERE `key`=? LIMIT 1");
    $st->execute([$key]);
    $v = $st->fetchColumn();
    return ($v === false || $v === null) ? $default : (string)$v;
  }

  public static function set(string $key, string $value, int $adminId = 0): void {
    $pdo = DB::pdo();
    $pdo->prepare(
      "INSERT INTO settings(`key`,`value`,updated_by,updated_at)
       VALUES(?,?,?,NOW())
       ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), updated_by=VALUES(updated_by), updated_at=NOW()"
    )->execute([$key, $value, $adminId]);
  }
}
