<?php
declare(strict_types=1);

use App\DB;
use App\Helpers\Env;

require __DIR__ . '/../src/bootstrap.php';

$pdo = DB::pdo();
$files = glob(__DIR__ . '/../database/migrations/*.sql') ?: [];
sort($files);
foreach ($files as $f) {
  echo 'Applying: ' . $f . PHP_EOL;
  $pdo->exec((string)file_get_contents($f));
}

$seed = __DIR__ . '/../database/seeds/001_seed.sql';
if (file_exists($seed)) {
  echo 'Seeding: ' . $seed . PHP_EOL;
  $pdo->exec((string)file_get_contents($seed));
}

$adminEmail = (string)Env::get('ADMIN_EMAIL', 'admin@local.test');
$adminPass  = (string)Env::get('ADMIN_PASS', '123456');
$st = $pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
$st->execute([$adminEmail]);
$exists = $st->fetch();

if (!$exists) {
  $hash = password_hash($adminPass, PASSWORD_DEFAULT);
  $pdo->prepare("INSERT INTO users(role,name,email,password_hash,status,created_at,updated_at)
                 VALUES('admin','Admin',?,?, 'active', NOW(), NOW())")
      ->execute([$adminEmail, $hash]);
  echo 'Admin created: ' . $adminEmail . ' / ' . $adminPass . PHP_EOL;
}

echo 'Done.' . PHP_EOL;
