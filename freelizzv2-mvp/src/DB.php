<?php
declare(strict_types=1);

namespace App;

use PDO;
use App\Helpers\Env;

final class DB {
  private static ?PDO $pdo = null;

  public static function pdo(): PDO {
    if (self::$pdo) return self::$pdo;

    $host = Env::get('DB_HOST','db');
    $port = (int) Env::get('DB_PORT',3306);
    $name = Env::get('DB_NAME','freelizz');
    $user = Env::get('DB_USER','freelizz');
    $pass = Env::get('DB_PASS','freelizz123');

    $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $name . ';charset=utf8mb4';
    self::$pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return self::$pdo;
  }
}

