<?php
declare(strict_types=1);

namespace App\Helpers;

final class ErrorHandler
{
  public static function register(): void
  {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');

    set_error_handler([self::class, 'onError']);
    set_exception_handler([self::class, 'onException']);
  }

  public static function onError(int $severity, string $message, string $file, int $line): bool
  {
    // Converte warnings/notices em ErrorException para cair no handler único
    if (!(error_reporting() & $severity)) return false;
    throw new \ErrorException($message, 0, $severity, $file, $line);
  }

  public static function onException(\Throwable $e): void
  {
    self::log($e);

    $isCli = (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg');

    if ($isCli) {
      $msg = "[EXCEPTION] {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\n";
      $msg .= $e->getTraceAsString() . "\n";
      fwrite(STDERR, $msg);
      exit(1);
    }

    // Web
    if (!headers_sent()) {
      http_response_code(500);
      header('Content-Type: text/html; charset=UTF-8');
    }

    // Evita vazar detalhes em produção
    $debug = (string)(getenv('APP_DEBUG') ?: '0');
    if ($debug === '1') {
      echo "<h1>Erro interno</h1>";
      echo "<pre>" . htmlspecialchars((string)$e) . "</pre>";
      return;
    }

    echo "<h1>Erro interno</h1><p>Veja storage/logs/app.log</p>";
  }

  private static function log(\Throwable $e): void
  {
    $dir = __DIR__ . '/../../storage/logs';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);

    $file = $dir . '/app.log';
    $ts = gmdate('Y-m-d\TH:i:s\Z');
    $line = "[{$ts}] EXCEPTION {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\n";
    $line .= $e->getTraceAsString() . "\n\n";
    @file_put_contents($file, $line, FILE_APPEND);
  }
}
