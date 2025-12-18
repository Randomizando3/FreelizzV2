<?php
declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
$dirs = [$root . '/src', $root . '/scripts', $root . '/public'];

$scanned = 0;
$fixed = 0;

foreach ($dirs as $dir) {
  if (!is_dir($dir)) continue;

  $it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
  );

  foreach ($it as $f) {
    if (!$f->isFile()) continue;
    if (strtolower($f->getExtension()) !== 'php') continue;

    $path = $f->getPathname();
    $txt  = file_get_contents($path);
    $orig = $txt;
    $scanned++;

    // 1) Corrige namespace/use em "código" (não mexe em strings PHP)
    $lines = preg_split("/\r\n|\n|\r/", $txt);
    foreach ($lines as &$line) {
      // namespace App\\X -> namespace App\X (reduz repetidamente)
      if (preg_match('/^\s*namespace\s+App\\\\+/', $line)) {
        while (strpos($line, 'namespace App\\\\') !== false) {
          $line = str_replace('namespace App\\\\', 'namespace App\\', $line);
        }
      }

      // use App\\X -> use App\X
      if (preg_match('/^\s*use\s+App\\\\+/', $line)) {
        while (strpos($line, 'use App\\\\') !== false) {
          $line = str_replace('use App\\\\', 'use App\\', $line);
        }
      }

      // \App\\X -> \App\X
      if (preg_match('/^\s*\\\\App\\\\+/', $line)) {
        while (strpos($line, '\\App\\\\') !== false) {
          $line = str_replace('\\App\\\\', '\\App\\', $line);
        }
      }
    }
    unset($line);
    $txt = implode("\n", $lines);

    // 2) Corrige strings de rota (quando vieram com 4 barras e viraram 2 no runtime)
    // App\\Controllers\\ -> App\\Controllers\\
    while (strpos($txt, 'App\\\\\\\\Controllers\\\\\\\\') !== false) {
      $txt = str_replace('App\\\\\\\\Controllers\\\\\\\\', 'App\\Controllers\\', $txt);
    }

    // 3) Corrige prefix do autoloader (quando veio 'App\\\\' em vez de 'App\\')
    while (strpos($txt, "\$prefix = 'App\\\\\\\\';") !== false) {
      $txt = str_replace("\$prefix = 'App\\\\\\\\';", "\$prefix = 'App\\';", $txt);
    }

    // 4) Corrige o str_replace do autoloader para sempre: str_replace('\\', '/', $relative)
    $txt = preg_replace(
      "/str_replace\('\\\\{2,}',\s*'\/',\s*\$relative\)/",
      "str_replace('\\\\', '/', \$relative)",
      $txt
    );

    if ($txt !== $orig) {
      file_put_contents($path, $txt);
      $fixed++;
    }
  }
}

echo "Scanned: {$scanned}\n";
echo "Fixed:   {$fixed}\n";
