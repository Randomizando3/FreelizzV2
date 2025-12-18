<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\DB;
use App\Helpers\Env;

$pdo = DB::pdo();

// Tabela de controle (DDL: sem transação)
$pdo->exec("
  CREATE TABLE IF NOT EXISTS schema_migrations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL UNIQUE,
    applied_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

function alreadyApplied(\PDO $pdo, string $filename): bool {
  $st = $pdo->prepare("SELECT 1 FROM schema_migrations WHERE filename=? LIMIT 1");
  $st->execute([$filename]);
  return (bool)$st->fetchColumn();
}

function markApplied(\PDO $pdo, string $filename): void {
  $pdo->prepare("INSERT INTO schema_migrations(filename, applied_at) VALUES(?, NOW())")
      ->execute([$filename]);
}

function loadSql(string $path): array {
  $sql = (string)file_get_contents($path);

  // remove comentários de linha
  $sql = preg_replace('/^\s*--.*$/m', '', $sql);
  $sql = preg_replace('/^\s*#.*$/m', '', $sql);

  // split simples por ';' (suficiente p/ migrations sem procedures)
  $parts = array_filter(array_map('trim', explode(';', $sql)));
  return array_values($parts);
}

$files = glob(__DIR__ . '/../database/migrations/*.sql');
sort($files);

foreach ($files as $f) {
  $filename = basename($f);

  if (alreadyApplied($pdo, $filename)) {
    echo "Skipping (already applied): {$filename}\n";
    continue;
  }

  echo "Applying: {$filename}\n";

  $parts = loadSql($f);

  try {
    foreach ($parts as $stmt) {
      if ($stmt === '') continue;
      $pdo->exec($stmt);
    }
    markApplied($pdo, $filename);
  } catch (\Throwable $e) {
    // Sem rollback: DDL no MySQL auto-commita
    echo "FAILED on {$filename}: {$e->getMessage()}\n";
    throw $e;
  }
}

// Seeds (normalmente DML, pode usar transação)
$seedFiles = [
  __DIR__ . '/../database/seeds/001_seed.sql',
  __DIR__ . '/../database/seeds/002_seed_admin.sql',
  __DIR__ . '/../database/seeds/003_plans_patch.sql',
];

foreach ($seedFiles as $seed) {
  if (!file_exists($seed)) continue;

  $seedName = 'seed:' . basename($seed);

  if (alreadyApplied($pdo, $seedName)) {
    echo "Skipping seed (already applied): {$seedName}\n";
    continue;
  }

  echo "Seeding: {$seedName}\n";
  $parts = loadSql($seed);

  $pdo->beginTransaction();
  try {
    foreach ($parts as $stmt) {
      if ($stmt === '') continue;
      $pdo->exec($stmt);
    }
    $pdo->commit();
    markApplied($pdo, $seedName);
  } catch (\Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "FAILED seed {$seedName}: {$e->getMessage()}\n";
    throw $e;
  }
}

// Admin padrão (idempotente)
$adminEmail = (string)Env::get('ADMIN_EMAIL', 'admin@local.test');
$adminPass  = (string)Env::get('ADMIN_PASS', '123456');

$st = $pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$st->execute([$adminEmail]);
$exists = $st->fetch();

if (!$exists) {
  $hash = password_hash($adminPass, PASSWORD_DEFAULT);
  $pdo->prepare("INSERT INTO users(role,name,email,password_hash,status,created_at,updated_at)
                 VALUES('admin','Admin',?,?, 'active', NOW(), NOW())")
      ->execute([$adminEmail, $hash]);
  echo "Admin created: {$adminEmail} / {$adminPass}\n";
}

echo "Done.\n";
