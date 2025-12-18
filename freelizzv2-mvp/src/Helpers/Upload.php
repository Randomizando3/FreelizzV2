<?php
declare(strict_types=1);

namespace App\Helpers;

final class Upload {
  public static function save(array $file, array $opts = []): string {
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
      throw new \RuntimeException('Upload inválido.');
    }

    $max = (int)($opts['max_bytes'] ?? 5_000_000);
    if ((int)$file['size'] > $max) throw new \RuntimeException('Arquivo muito grande.');

    $allowedExt = $opts['ext'] ?? ['jpg','jpeg','png','webp'];
    $name = (string)($file['name'] ?? '');
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) throw new \RuntimeException('Extensão não permitida.');

    $sub = $opts['subdir'] ?? 'misc';
    $base = __DIR__ . '/../../storage/uploads/' . $sub . '/' . date('Y') . '/' . date('m');
    if (!is_dir($base)) @mkdir($base, 0775, true);

    $safe = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $base . '/' . $safe;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
      throw new \RuntimeException('Falha ao salvar arquivo.');
    }

    // retorna path público relativo via /uploads
    $rel = '/uploads/' . $sub . '/' . date('Y') . '/' . date('m') . '/' . $safe;
    return $rel;
  }
}
