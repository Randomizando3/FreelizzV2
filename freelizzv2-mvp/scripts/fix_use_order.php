<?php
declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
$targets = [$root . '/public', $root . '/scripts'];

$scanned = 0;
$fixed = 0;

foreach ($targets as $dir) {
  if (!is_dir($dir)) continue;

  $it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
  );

  foreach ($it as $f) {
    if (!$f->isFile() || strtolower($f->getExtension()) !== 'php') continue;

    $path = $f->getPathname();
    $txt  = file_get_contents($path);
    $orig = $txt;

    // Só tenta corrigir se houver "use App\" e "require" no topo
    if (strpos($txt, "use App\\") === false || strpos($txt, "require ") === false) {
      $scanned++;
      continue;
    }

    $lines = preg_split("/\r\n|\n|\r/", $txt);

    // Localiza bloco de header (até a primeira linha de "código real")
    $headerEnd = 0;
    for ($i = 0; $i < count($lines); $i++) {
      $l = $lines[$i];
      if (preg_match('/^\s*(<\?php|\?>)\s*$/', $l)) { $headerEnd = $i+1; continue; }
      if (preg_match('/^\s*declare\s*\(/', $l)) { $headerEnd = $i+1; continue; }
      if (preg_match('/^\s*(\/\/.*|\/\*.*|\*.*|\*\/)\s*$/', $l)) { $headerEnd = $i+1; continue; }
      if (preg_match('/^\s*$/', $l)) { $headerEnd = $i+1; continue; }

      // permitimos require/ include / use ainda no header
      if (preg_match('/^\s*(require|include)(_once)?\b/', $l)) { $headerEnd = $i+1; continue; }
      if (preg_match('/^\s*use\s+/', $l)) { $headerEnd = $i+1; continue; }

      // primeira linha que não é header
      break;
    }

    $header = array_slice($lines, 0, $headerEnd);
    $rest   = array_slice($lines, $headerEnd);

    // Se no header existir require antes de use, move require(s) para depois do último use.
    $requireLines = [];
    $useLines = [];
    $otherHeader = [];

    foreach ($header as $l) {
      if (preg_match('/^\s*(require|include)(_once)?\b/', $l)) {
        $requireLines[] = $l;
      } elseif (preg_match('/^\s*use\s+/', $l)) {
        $useLines[] = $l;
      } else {
        $otherHeader[] = $l;
      }
    }

    // só aplica se existir use e require no header
    if ($requireLines && $useLines) {
      // Reconstrói header mantendo ordem: php/declare/comentários -> use -> require -> linha em branco
      // Para não bagunçar, preserva $otherHeader como estava, mas remove require/use dele.
      $newHeader = [];

      // mantém as primeiras linhas (php + declare + comentários + vazias) na ordem original,
      // mas sem os require/use (já separados)
      foreach ($otherHeader as $l) $newHeader[] = $l;

      // garante uma linha em branco antes dos use (se necessário)
      if ($newHeader && trim(end($newHeader)) !== '') $newHeader[] = '';

      foreach ($useLines as $l) $newHeader[] = $l;

      $newHeader[] = '';
      foreach ($requireLines as $l) $newHeader[] = $l;
      $newHeader[] = '';

      $txt = implode("\n", array_merge($newHeader, $rest));
    }

    $scanned++;
    if ($txt !== $orig) {
      file_put_contents($path, $txt);
      $fixed++;
    }
  }
}

echo "Scanned: {$scanned}\n";
echo "Fixed:   {$fixed}\n";
