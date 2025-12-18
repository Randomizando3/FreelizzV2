<?php
declare(strict_types=1);

use App\DB;

require __DIR__ . '/../src/bootstrap.php';

$tries = 30;
while (true) {
  try {
    $pdo = DB::pdo();
    $pdo->query('SELECT 1');
    break;
  } catch (\Throwable $e) {
    $tries--;
    if ($tries <= 0) throw $e;
    echo "Waiting for DB... ({$tries})\n";
    sleep(1);
  }
}

$files = glob(__DIR__ . '/../database/migrations/*.sql') ?: [];
sort($files);

foreach ($files as $f) {
  echo "Applying: $f\n";
  $pdo->exec((string)file_get_contents($f));
}

$seeds = glob(__DIR__ . '/../database/seeds/*.sql') ?: [];
sort($seeds);
foreach ($seeds as $s) {
  echo "Seeding: $s\n";
  $pdo->exec((string)file_get_contents($s));
}

echo "Done.\n";
