#NoEnv
#SingleInstance Force
SetBatchLines, -1
FileEncoding, UTF-8

base := A_ScriptDir . "\freelizzv2-mvp"
if (!InStr(FileExist(base), "D")) {
  MsgBox, 16, Erro, Pasta nao encontrada:`n%base%
  ExitApp
}

bs  := Chr(92)      ; "\"
dbl := bs . bs      ; "\\"

WriteUtf8NoBom(full, content) {
  SplitPath, full, , dir
  FileCreateDir, %dir%
  f := FileOpen(full, "w", "UTF-8-RAW")
  if (!IsObject(f)) {
    MsgBox, 16, Erro, Falha ao escrever:`n%full%
    ExitApp
  }
  f.Write(content)
  f.Close()
}

; =========================
; 1) Sobrescrever arquivos críticos (conteúdo 100% válido)
; =========================

; src/bootstrap.php
c := ""
c .= "<?php`n"
c .= "declare(strict_types=1);`n"
c .= "`n"
c .= "session_start();`n"
c .= "`n"
c .= "spl_autoload_register(function (string $class): void {`n"
c .= "  $prefix = 'App\\';`n"
c .= "  if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;`n"
c .= "  $relative = substr($class, strlen($prefix));`n"
c .= "  $path = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';`n"
c .= "  if (file_exists($path)) require $path;`n"
c .= "});`n"
c .= "`n"
c .= "App\Helpers\Env::load(__DIR__ . '/../.env');`n"
c .= "App\Helpers\ErrorHandler::register();`n"
WriteUtf8NoBom(base . "\src\bootstrap.php", c)

; src/Helpers/ErrorHandler.php
c := ""
c .= "<?php`n"
c .= "declare(strict_types=1);`n"
c .= "`n"
c .= "namespace App\Helpers;`n"
c .= "`n"
c .= "final class ErrorHandler {`n"
c .= "  public static function register(): void {`n"
c .= "    set_exception_handler([self::class, 'onException']);`n"
c .= "  }`n"
c .= "`n"
c .= "  public static function onException(\Throwable $e): void {`n"
c .= "    self::log('EXCEPTION ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());`n"
c .= "    http_response_code(500);`n"
c .= "    echo '<h1>Erro interno</h1><p>Veja storage/logs/app.log</p>';`n"
c .= "  }`n"
c .= "`n"
c .= "  private static function log(string $line): void {`n"
c .= "    $dir = __DIR__ . '/../../storage/logs';`n"
c .= "    if (!is_dir($dir)) @mkdir($dir, 0777, true);`n"
c .= "    @file_put_contents($dir . '/app.log', '[' . date('c') . '] ' . $line . PHP_EOL, FILE_APPEND);`n"
c .= "  }`n"
c .= "}`n"
WriteUtf8NoBom(base . "\src\Helpers\ErrorHandler.php", c)

; scripts/migrate.php (use antes do require + backslash simples no namespace)
c := ""
c .= "<?php`n"
c .= "declare(strict_types=1);`n"
c .= "`n"
c .= "use App\DB;`n"
c .= "use App\Helpers\Env;`n"
c .= "`n"
c .= "require __DIR__ . '/../src/bootstrap.php';`n"
c .= "`n"
c .= "$pdo = DB::pdo();`n"
c .= "$files = glob(__DIR__ . '/../database/migrations/*.sql') ?: [];`n"
c .= "sort($files);`n"
c .= "foreach ($files as $f) {`n"
c .= "  echo 'Applying: ' . $f . PHP_EOL;`n"
c .= "  $pdo->exec((string)file_get_contents($f));`n"
c .= "}`n"
c .= "`n"
c .= "$seed = __DIR__ . '/../database/seeds/001_seed.sql';`n"
c .= "if (file_exists($seed)) {`n"
c .= "  echo 'Seeding: ' . $seed . PHP_EOL;`n"
c .= "  $pdo->exec((string)file_get_contents($seed));`n"
c .= "}`n"
c .= "`n"
c .= "$adminEmail = (string)Env::get('ADMIN_EMAIL', 'admin@local.test');`n"
c .= "$adminPass  = (string)Env::get('ADMIN_PASS', '123456');`n"
c .= "$st = $pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1');`n"
c .= "$st->execute([$adminEmail]);`n"
c .= "$exists = $st->fetch();`n"
c .= "`n"
c .= "if (!$exists) {`n"
c .= "  $hash = password_hash($adminPass, PASSWORD_DEFAULT);`n"
c .= "  $pdo->prepare(""INSERT INTO users(role,name,email,password_hash,status,created_at,updated_at)`n"
c .= "                 VALUES('admin','Admin',?,?, 'active', NOW(), NOW())"")`n"
c .= "      ->execute([$adminEmail, $hash]);`n"
c .= "  echo 'Admin created: ' . $adminEmail . ' / ' . $adminPass . PHP_EOL;`n"
c .= "}`n"
c .= "`n"
c .= "echo 'Done.' . PHP_EOL;`n"
WriteUtf8NoBom(base . "\scripts\migrate.php", c)

; =========================
; 2) Normalizar namespace/use em TODOS os .php (sem mexer em strings)
; =========================
fixed := 0
scanned := 0

Loop, Files, % base "\*.php", R
{
  scanned++
  file := A_LoopFileFullPath
  FileRead, txt, %file%
  if (ErrorLevel)
    continue

  out := ""
  changed := false

  Loop, Parse, txt, `n, `r
  {
    line := A_LoopField

    ; Corrige apenas linhas de namespace/use (colapsa \\... para \...)
    if (RegExMatch(line, "^\s*namespace\s+")) {
      while InStr(line, dbl) {
        line := StrReplace(line, dbl, bs)
        changed := true
      }
    } else if (RegExMatch(line, "^\s*use\s+")) {
      while InStr(line, dbl) {
        line := StrReplace(line, dbl, bs)
        changed := true
      }
    }

    ; Corrige type-hint \\Throwable -> \Throwable (se existir)
    if (InStr(line, dbl . "Throwable")) {
      while InStr(line, dbl . "Throwable") {
        line := StrReplace(line, dbl . "Throwable", bs . "Throwable")
        changed := true
      }
    }

    out .= line . "`n"
  }

  if (changed) {
    WriteUtf8NoBom(file, out)
    fixed++
  }
}

MsgBox, 64, OK, Reparacao concluida.`nArquivos varridos: %scanned%`nArquivos ajustados: %fixed%`n`nAgora rode migrate novamente.
ExitApp
