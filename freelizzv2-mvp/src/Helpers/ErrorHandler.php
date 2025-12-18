<?php
declare(strict_types=1);

namespace App\Helpers;

final class ErrorHandler {
  public static function register(): void {
    set_exception_handler([self::class, 'onException']);
  }

  public static function onException(\Throwable $e): void {
    self::log('EXCEPTION ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo '<h1>Erro interno</h1><p>Veja storage/logs/app.log</p>';
  }

  private static function log(string $line): void {
    $dir = __DIR__ . '/../../storage/logs';
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    @file_put_contents($dir . '/app.log', '[' . date('c') . '] ' . $line . PHP_EOL, FILE_APPEND);
  }
}
