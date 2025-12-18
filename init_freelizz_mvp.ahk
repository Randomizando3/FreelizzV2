#NoEnv
#SingleInstance Force
SetBatchLines, -1
FileEncoding, UTF-8

base := A_ScriptDir . "\freelizzv2-mvp"

EnsureDir(path) {
  FileCreateDir, %path%
}

WriteFile(relPath, content) {
  global base
  full := base . "\" . relPath
  SplitPath, full, , dir
  FileCreateDir, %dir%

  f := FileOpen(full, "w", "UTF-8-RAW")
  if (!IsObject(f)) {
    MsgBox, 16, Erro, Falha ao criar:`n%full%
    ExitApp
  }
  f.Write(content)
  f.Close()
}

; =========================
; PASTAS
; =========================
EnsureDir(base)
EnsureDir(base "\public")
EnsureDir(base "\public\assets\css")
EnsureDir(base "\public\assets\js")
EnsureDir(base "\public\uploads")
EnsureDir(base "\public\uploads\avatars")
EnsureDir(base "\public\uploads\verifications")
EnsureDir(base "\src")
EnsureDir(base "\src\Controllers")
EnsureDir(base "\src\Middlewares")
EnsureDir(base "\src\Helpers")
EnsureDir(base "\src\Services")
EnsureDir(base "\views\layouts")
EnsureDir(base "\views\public")
EnsureDir(base "\views\auth")
EnsureDir(base "\views\client")
EnsureDir(base "\views\freelancer")
EnsureDir(base "\views\admin")
EnsureDir(base "\database\migrations")
EnsureDir(base "\database\seeds")
EnsureDir(base "\scripts")
EnsureDir(base "\storage\logs")

; =========================
; docker-compose.yml
; =========================
content := ""
content .= "services:`n"
content .= "  web:`n"
content .= "    build: .`n"
content .= "    container_name: freelizz_web`n"
content .= "    ports:`n"
content .= "      - '8787:80'`n"
content .= "    volumes:`n"
content .= "      - ./:/var/www/html`n"
content .= "    env_file:`n"
content .= "      - .env`n"
content .= "    depends_on:`n"
content .= "      - db`n"
content .= "`n"
content .= "  db:`n"
content .= "    image: mysql:8.4`n"
content .= "    container_name: freelizz_db`n"
content .= "    ports:`n"
content .= "      - '3390:3306'`n"
content .= "    environment:`n"
content .= "      MYSQL_DATABASE: ${DB_NAME}`n"
content .= "      MYSQL_USER: ${DB_USER}`n"
content .= "      MYSQL_PASSWORD: ${DB_PASS}`n"
content .= "      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASS}`n"
content .= "    volumes:`n"
content .= "      - freelizz_mysql:/var/lib/mysql`n"
content .= "`n"
content .= "volumes:`n"
content .= "  freelizz_mysql:`n"
WriteFile("docker-compose.yml", content)

; =========================
; Dockerfile
; =========================
content := ""
content .= "FROM php:8.3-apache`n"
content .= "`n"
content .= "RUN a2enmod rewrite headers`n"
content .= "RUN docker-php-ext-install pdo pdo_mysql`n"
content .= "`n"
content .= "# DocumentRoot -> /public`n"
content .= "RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \`n"
content .= " && printf '\n<Directory /var/www/html/public>\n  AllowOverride All\n  Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf`n"
content .= "`n"
content .= "WORKDIR /var/www/html`n"
WriteFile("Dockerfile", content)

; =========================
; .env e .env.example (sem aspas para simplificar)
; =========================
content := ""
content .= "APP_NAME=FreelizzV2`n"
content .= "APP_URL=http://localhost:8787`n"
content .= "APP_ENV=local`n"
content .= "APP_DEBUG=1`n"
content .= "`n"
content .= "DB_HOST=db`n"
content .= "DB_PORT=3306`n"
content .= "DB_NAME=freelizz`n"
content .= "DB_USER=freelizz`n"
content .= "DB_PASS=freelizz123`n"
content .= "DB_ROOT_PASS=root123`n"
content .= "`n"
content .= "MP_ACCESS_TOKEN=`n"
content .= "MP_WEBHOOK_SECRET=`n"
content .= "`n"
content .= "ADMIN_EMAIL=admin@local.test`n"
content .= "ADMIN_PASS=123456`n"
WriteFile(".env.example", content)
WriteFile(".env", content)

; =========================
; public/.htaccess (agora %{} é literal, sem escape)
; =========================
content := ""
content .= "RewriteEngine On`n"
content .= "`n"
content .= "RewriteCond %{REQUEST_FILENAME} -f [OR]`n"
content .= "RewriteCond %{REQUEST_FILENAME} -d`n"
content .= "RewriteRule ^ - [L]`n"
content .= "`n"
content .= "RewriteRule ^ index.php [QSA,L]`n"
WriteFile("public\.htaccess", content)

; =========================
; public/uploads/.htaccess
; =========================
content := ""
content .= "Options -Indexes`n"
content .= "`n"
content .= "RemoveHandler .php .phtml .php3 .php4 .php5 .phps`n"
content .= "RemoveType .php .phtml .php3 .php4 .php5 .phps`n"
content .= "`n"
content .= "<FilesMatch ""\.(php|phtml|php3|php4|php5|phps)$"">`n"
content .= "  Require all denied`n"
content .= "</FilesMatch>`n"
WriteFile("public\uploads\.htaccess", content)

; =========================
; CSS
; =========================
content := ""
content .= ":root{--bg:#f6f7fb;--card:#fff;--txt:#111827;--muted:#6b7280;--pri:#2563eb;--bd:#e5e7eb;}`n"
content .= "*{box-sizing:border-box}`n"
content .= "body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;background:var(--bg);color:var(--txt);}`n"
content .= "a{color:var(--pri);text-decoration:none}`n"
content .= ".container{max-width:1100px;margin:0 auto;padding:16px}`n"
content .= ".nav{background:#fff;border-bottom:1px solid var(--bd)}`n"
content .= ".nav .row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 16px;max-width:1100px;margin:0 auto}`n"
content .= ".brand{font-weight:800}`n"
content .= ".card{background:var(--card);border:1px solid var(--bd);border-radius:12px;padding:14px}`n"
content .= ".grid{display:grid;grid-template-columns:repeat(12,1fr);gap:12px}`n"
content .= ".col-8{grid-column:span 8}.col-4{grid-column:span 4}.col-12{grid-column:span 12}`n"
content .= "@media (max-width:900px){.col-8,.col-4{grid-column:span 12}}`n"
content .= ".btn{display:inline-block;border:1px solid var(--bd);background:var(--pri);color:#fff;padding:10px 12px;border-radius:10px;cursor:pointer}`n"
content .= ".btn.secondary{background:#fff;color:var(--txt)}`n"
content .= ".input{width:100%;padding:10px;border:1px solid var(--bd);border-radius:10px}`n"
content .= ".muted{color:var(--muted)}`n"
content .= ".table{width:100%;border-collapse:collapse}`n"
content .= ".table th,.table td{border-bottom:1px solid var(--bd);padding:10px;text-align:left}`n"
content .= ".badge{display:inline-block;padding:4px 8px;border-radius:999px;font-size:12px;border:1px solid var(--bd);background:#fff}`n"
WriteFile("public\assets\css\app.css", content)

; =========================
; JS
; =========================
content := ""
content .= "function confirmPost(formId,msg){`n"
content .= "  if(confirm(msg||'Confirmar?')) document.getElementById(formId).submit();`n"
content .= "}`n"
WriteFile("public\assets\js\app.js", content)

; =========================
; src/bootstrap.php
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "session_start();`n"
content .= "`n"
content .= "spl_autoload_register(function(string $class): void {`n"
content .= "  $prefix = 'App\\\\';`n"
content .= "  if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;`n"
content .= "  $relative = substr($class, strlen($prefix));`n"
content .= "  $path = __DIR__ . '/' . str_replace('\\\\\\\\', '/', $relative) . '.php';`n"
content .= "  if (file_exists($path)) require $path;`n"
content .= "});`n"
content .= "`n"
content .= "\\App\\Helpers\\Env::load(__DIR__ . '/../.env');`n"
content .= "\\App\\Helpers\\ErrorHandler::register();`n"
WriteFile("src\bootstrap.php", content)

; =========================
; Helpers Env
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "namespace App\\Helpers;`n"
content .= "`n"
content .= "final class Env {`n"
content .= "  public static function load(string $path): void {`n"
content .= "    if (!file_exists($path)) return;`n"
content .= "    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);`n"
content .= "    foreach ($lines as $line) {`n"
content .= "      $line = trim($line);`n"
content .= "      if ($line === '' || str_starts_with($line, '#')) continue;`n"
content .= "      [$k,$v] = array_pad(explode('=', $line, 2), 2, '');`n"
content .= "      $k = trim($k); $v = trim($v);`n"
content .= "      if ($k !== '' && getenv($k) === false) {`n"
content .= "        putenv($k . '=' . $v); $_ENV[$k] = $v;`n"
content .= "      }`n"
content .= "    }`n"
content .= "  }`n"
content .= "  public static function get(string $key, $default=null) {`n"
content .= "    $v = getenv($key);`n"
content .= "    return ($v === false || $v === '') ? $default : $v;`n"
content .= "  }`n"
content .= "}`n"
WriteFile("src\Helpers\Env.php", content)

; =========================
; Helpers ErrorHandler
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "namespace App\\Helpers;`n"
content .= "`n"
content .= "final class ErrorHandler {`n"
content .= "  public static function register(): void {`n"
content .= "    set_exception_handler([self::class, 'onException']);`n"
content .= "  }`n"
content .= "  public static function onException(\\Throwable $e): void {`n"
content .= "    self::log('EXCEPTION ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());`n"
content .= "    http_response_code(500);`n"
content .= "    echo '<h1>Erro interno</h1><p>Veja storage/logs/app.log</p>';`n"
content .= "  }`n"
content .= "  private static function log(string $line): void {`n"
content .= "    $dir = __DIR__ . '/../../storage/logs';`n"
content .= "    if (!is_dir($dir)) @mkdir($dir, 0777, true);`n"
content .= "    @file_put_contents($dir . '/app.log', '[' . date('c') . '] ' . $line . PHP_EOL, FILE_APPEND);`n"
content .= "  }`n"
content .= "}`n"
WriteFile("src\Helpers\ErrorHandler.php", content)

; =========================
; Helpers CSRF
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "namespace App\\Helpers;`n"
content .= "`n"
content .= "final class Csrf {`n"
content .= "  public static function token(): string {`n"
content .= "    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(16));`n"
content .= "    return (string)$_SESSION['_csrf'];`n"
content .= "  }`n"
content .= "  public static function check(): void {`n"
content .= "    $sent = (string)($_POST['_csrf'] ?? '');`n"
content .= "    $ok = isset($_SESSION['_csrf']) && hash_equals((string)$_SESSION['_csrf'], $sent);`n"
content .= "    if (!$ok) {`n"
content .= "      http_response_code(419);`n"
content .= "      echo '<h1>419</h1><p>CSRF inválido</p>';`n"
content .= "      exit;`n"
content .= "    }`n"
content .= "  }`n"
content .= "}`n"
WriteFile("src\Helpers\Csrf.php", content)

; =========================
; src/DB.php
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "namespace App;`n"
content .= "`n"
content .= "use PDO;`n"
content .= "use App\\Helpers\\Env;`n"
content .= "`n"
content .= "final class DB {`n"
content .= "  private static ?PDO $pdo = null;`n"
content .= "  public static function pdo(): PDO {`n"
content .= "    if (self::$pdo) return self::$pdo;`n"
content .= "    $host = Env::get('DB_HOST','db');`n"
content .= "    $port = (int)Env::get('DB_PORT',3306);`n"
content .= "    $name = Env::get('DB_NAME','freelizz');`n"
content .= "    $user = Env::get('DB_USER','freelizz');`n"
content .= "    $pass = Env::get('DB_PASS','freelizz123');`n"
content .= "    $dsn = ""mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4"";`n"
content .= "    self::$pdo = new PDO($dsn, $user, $pass, [`n"
content .= "      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,`n"
content .= "      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,`n"
content .= "    ]);`n"
content .= "    return self::$pdo;`n"
content .= "  }`n"
content .= "}`n"
WriteFile("src\DB.php", content)

; =========================
; src/Router.php
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "namespace App;`n"
content .= "`n"
content .= "final class Router {`n"
content .= "  private array $routes = [];`n"
content .= "  public function get(string $path, string $handler): void { $this->add('GET', $path, $handler); }`n"
content .= "  public function post(string $path, string $handler): void { $this->add('POST', $path, $handler); }`n"
content .= "  private function add(string $method, string $path, string $handler): void {`n"
content .= "    $pattern = preg_replace('#\\{([a-zA-Z_][a-zA-Z0-9_]*)\\}#', '(?P<$1>[^/]+)', $path);`n"
content .= "    $pattern = '#^' . rtrim($pattern, '/') . '/?$#';`n"
content .= "    $this->routes[] = ['method'=>$method,'pattern'=>$pattern,'handler'=>$handler];`n"
content .= "  }`n"
content .= "  public function dispatch(): void {`n"
content .= "    $uri = (string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');`n"
content .= "    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';`n"
content .= "    foreach ($this->routes as $r) {`n"
content .= "      if ($r['method'] !== $method) continue;`n"
content .= "      if (!preg_match($r['pattern'], $uri, $m)) continue;`n"
content .= "      $params = []; foreach ($m as $k=>$v) if (!is_int($k)) $params[$k]=$v;`n"
content .= "      return $this->runHandler($r['handler'], $params);`n"
content .= "    }`n"
content .= "    http_response_code(404); echo '<h1>404</h1>';`n"
content .= "  }`n"
content .= "  private function runHandler(string $handler, array $params): void {`n"
content .= "    [$class, $method] = explode('@', $handler, 2);`n"
content .= "    $obj = new $class();`n"
content .= "    $obj->$method($params);`n"
content .= "  }`n"
content .= "}`n"
WriteFile("src\Router.php", content)

; =========================
; Controllers Base/Public/Auth
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "namespace App\\Controllers;`n"
content .= "`n"
content .= "use App\\Helpers\\Csrf;`n"
content .= "`n"
content .= "abstract class BaseController {`n"
content .= "  protected function view(string $tpl, array $data=[]): void {`n"
content .= "    extract($data);`n"
content .= "    $csrf = Csrf::token();`n"
content .= "    $viewFile = __DIR__ . '/../../views/' . $tpl . '.php';`n"
content .= "    $layout = __DIR__ . '/../../views/layouts/main.php';`n"
content .= "    if (!file_exists($viewFile)) { echo '<h1>View não encontrada</h1>'; return; }`n"
content .= "    require $layout;`n"
content .= "  }`n"
content .= "  protected function redirect(string $to): void { header('Location: ' . $to); exit; }`n"
content .= "}`n"
WriteFile("src\Controllers\BaseController.php", content)

content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "namespace App\\Controllers;`n"
content .= "`n"
content .= "use App\\DB;`n"
content .= "`n"
content .= "final class PublicController extends BaseController {`n"
content .= "  public function home(): void { $this->view('public/home'); }`n"
content .= "  public function jobs(): void {`n"
content .= "    $pdo = DB::pdo();`n"
content .= "    $st = $pdo->query(""SELECT j.id,j.title,j.status,j.budget_type,j.published_at,c.name category_name`n"
content .= "                       FROM jobs j JOIN job_categories c ON c.id=j.category_id`n"
content .= "                       WHERE j.status='published'`n"
content .= "                       ORDER BY j.published_at DESC LIMIT 50"");`n"
content .= "    $jobs = $st->fetchAll();`n"
content .= "    $this->view('public/jobs', compact('jobs'));`n"
content .= "  }`n"
content .= "  public function jobShow(array $p): void {`n"
content .= "    $id = (int)($p['id'] ?? 0);`n"
content .= "    $pdo = DB::pdo();`n"
content .= "    $st = $pdo->prepare(""SELECT j.*, c.name category_name FROM jobs j JOIN job_categories c ON c.id=j.category_id WHERE j.id=?"");`n"
content .= "    $st->execute([$id]);`n"
content .= "    $job = $st->fetch();`n"
content .= "    if (!$job) { http_response_code(404); echo '<h1>Job não encontrado</h1>'; return; }`n"
content .= "    $this->view('public/job_show', compact('job'));`n"
content .= "  }`n"
content .= "}`n"
WriteFile("src\Controllers\PublicController.php", content)

content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "namespace App\\Controllers;`n"
content .= "`n"
content .= "use App\\DB;`n"
content .= "use App\\Helpers\\Csrf;`n"
content .= "`n"
content .= "final class AuthController extends BaseController {`n"
content .= "  public function loginForm(): void { $this->view('auth/login'); }`n"
content .= "  public function login(): void {`n"
content .= "    Csrf::check();`n"
content .= "    $email = strtolower(trim((string)($_POST['email'] ?? '')));`n"
content .= "    $pass  = (string)($_POST['password'] ?? '');`n"
content .= "    $pdo = DB::pdo();`n"
content .= "    $st = $pdo->prepare(""SELECT id,role,name,email,password_hash,status FROM users WHERE email=? LIMIT 1"");`n"
content .= "    $st->execute([$email]);`n"
content .= "    $u = $st->fetch();`n"
content .= "    if (!$u || $u['status'] !== 'active' || !password_verify($pass, (string)$u['password_hash'])) {`n"
content .= "      $this->redirect('/login?err=1');`n"
content .= "    }`n"
content .= "    $_SESSION['user'] = ['id'=>$u['id'],'role'=>$u['role'],'name'=>$u['name'],'email'=>$u['email']];`n"
content .= "    $this->redirect('/');`n"
content .= "  }`n"
content .= "  public function logout(): void {`n"
content .= "    Csrf::check();`n"
content .= "    session_destroy();`n"
content .= "    $this->redirect('/');`n"
content .= "  }`n"
content .= "}`n"
WriteFile("src\Controllers\AuthController.php", content)

; =========================
; public/index.php (rotas)
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "require __DIR__ . '/../src/bootstrap.php';`n"
content .= "`n"
content .= "use App\\Router;`n"
content .= "`n"
content .= "$router = new Router();`n"
content .= "$router->get('/', 'App\\\\Controllers\\\\PublicController@home');`n"
content .= "$router->get('/jobs', 'App\\\\Controllers\\\\PublicController@jobs');`n"
content .= "$router->get('/jobs/{id}', 'App\\\\Controllers\\\\PublicController@jobShow');`n"
content .= "$router->get('/login', 'App\\\\Controllers\\\\AuthController@loginForm');`n"
content .= "$router->post('/login', 'App\\\\Controllers\\\\AuthController@login');`n"
content .= "$router->post('/logout', 'App\\\\Controllers\\\\AuthController@logout');`n"
content .= "$router->dispatch();`n"
WriteFile("public\index.php", content)

; =========================
; Views
; =========================
content := ""
content .= "<?php`n"
content .= "  $u = $_SESSION['user'] ?? null;`n"
content .= "  ob_start();`n"
content .= "  require $viewFile;`n"
content .= "  $content = ob_get_clean();`n"
content .= "?>`n"
content .= "<!doctype html>`n"
content .= "<html lang=""pt-BR"">`n"
content .= "<head>`n"
content .= "  <meta charset=""utf-8"">`n"
content .= "  <meta name=""viewport"" content=""width=device-width,initial-scale=1"">`n"
content .= "  <title><?= htmlspecialchars((string)(getenv('APP_NAME') ?: 'FreelizzV2')) ?></title>`n"
content .= "  <link rel=""stylesheet"" href=""/assets/css/app.css"">`n"
content .= "</head>`n"
content .= "<body>`n"
content .= "<header class=""nav"">`n"
content .= "  <div class=""row"">`n"
content .= "    <div class=""brand""><a href=""/""><?= htmlspecialchars((string)(getenv('APP_NAME') ?: 'FreelizzV2')) ?></a></div>`n"
content .= "    <div>`n"
content .= "      <a href=""/jobs"">Jobs</a>`n"
content .= "      <span style=""margin:0 8px;color:#e5e7eb"">|</span>`n"
content .= "      <?php if (!$u): ?>`n"
content .= "        <a href=""/login"">Login</a>`n"
content .= "      <?php else: ?>`n"
content .= "        <form action=""/logout"" method=""post"" style=""display:inline"">`n"
content .= "          <input type=""hidden"" name=""_csrf"" value=""<?= htmlspecialchars($csrf) ?>"">`n"
content .= "          <button class=""btn secondary"" type=""submit"">Sair</button>`n"
content .= "        </form>`n"
content .= "      <?php endif; ?>`n"
content .= "    </div>`n"
content .= "  </div>`n"
content .= "</header>`n"
content .= "<main class=""container"">`n"
content .= "  <?= $content ?>`n"
content .= "</main>`n"
content .= "<script src=""/assets/js/app.js""></script>`n"
content .= "</body>`n"
content .= "</html>`n"
WriteFile("views\layouts\main.php", content)

content := ""
content .= "<div class=""grid"">`n"
content .= "  <section class=""card col-8"">`n"
content .= "    <h1>FreelizzV2 (MVP)</h1>`n"
content .= "    <p class=""muted"">Base do clone Workana: Docker (Apache+PHP) + MySQL, rotas públicas e login.</p>`n"
content .= "    <p><a class=""btn"" href=""/jobs"">Ver Jobs</a></p>`n"
content .= "  </section>`n"
content .= "  <aside class=""card col-4"">`n"
content .= "    <h3>Próximos passos</h3>`n"
content .= "    <ol class=""muted"">`n"
content .= "      <li>Rodar migrate.php (cria schema + seed)</li>`n"
content .= "      <li>Adicionar painel Cliente/Freelancer/Admin</li>`n"
content .= "      <li>Propostas, projetos, chat, Mercado Pago</li>`n"
content .= "    </ol>`n"
content .= "  </aside>`n"
content .= "</div>`n"
WriteFile("views\public\home.php", content)

content := ""
content .= "<h1>Jobs</h1>`n"
content .= "<div class=""card"">`n"
content .= "  <?php if (!$jobs): ?>`n"
content .= "    <p class=""muted"">Nenhum job publicado.</p>`n"
content .= "  <?php endif; ?>`n"
content .= "  <?php foreach ($jobs as $j): ?>`n"
content .= "    <p>`n"
content .= "      <a href=""/jobs/<?= (int)$j['id'] ?>""><b><?= htmlspecialchars($j['title']) ?></b></a>`n"
content .= "      <span class=""muted""> — <?= htmlspecialchars($j['category_name']) ?> · <?= htmlspecialchars($j['budget_type']) ?></span>`n"
content .= "    </p>`n"
content .= "  <?php endforeach; ?>`n"
content .= "</div>`n"
WriteFile("views\public\jobs.php", content)

content := ""
content .= "<h1><?= htmlspecialchars($job['title']) ?></h1>`n"
content .= "<p class=""muted""><?= htmlspecialchars($job['category_name']) ?> · <?= htmlspecialchars($job['budget_type']) ?></p>`n"
content .= "<div class=""card""><?= $job['description_html'] ?></div>`n"
WriteFile("views\public\job_show.php", content)

content := ""
content .= "<h1>Login</h1>`n"
content .= "<?php if (!empty($_GET['err'])): ?><p class=""muted"">Credenciais inválidas.</p><?php endif; ?>`n"
content .= "<div class=""card"">`n"
content .= "  <form method=""post"">`n"
content .= "    <input type=""hidden"" name=""_csrf"" value=""<?= htmlspecialchars($csrf) ?>"">`n"
content .= "    <label class=""muted"">E-mail</label>`n"
content .= "    <input class=""input"" name=""email"" type=""email"">`n"
content .= "    <label class=""muted"">Senha</label>`n"
content .= "    <input class=""input"" name=""password"" type=""password"">`n"
content .= "    <p style=""margin-top:10px""><button class=""btn"" type=""submit"">Entrar</button></p>`n"
content .= "  </form>`n"
content .= "</div>`n"
WriteFile("views\auth\login.php", content)

; =========================
; migrations + seed
; =========================
content := ""
content .= "CREATE TABLE IF NOT EXISTS users (`n"
content .= "  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,`n"
content .= "  role ENUM('admin','client','freelancer') NOT NULL,`n"
content .= "  name VARCHAR(120) NOT NULL,`n"
content .= "  email VARCHAR(190) NOT NULL,`n"
content .= "  password_hash VARCHAR(255) NOT NULL,`n"
content .= "  status ENUM('active','blocked') NOT NULL DEFAULT 'active',`n"
content .= "  created_at DATETIME NOT NULL,`n"
content .= "  updated_at DATETIME NULL,`n"
content .= "  UNIQUE KEY uq_users_email (email)`n"
content .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;`n"
content .= "`n"
content .= "CREATE TABLE IF NOT EXISTS job_categories (`n"
content .= "  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,`n"
content .= "  name VARCHAR(100) NOT NULL,`n"
content .= "  slug VARCHAR(120) NOT NULL,`n"
content .= "  UNIQUE KEY uq_cat_slug (slug)`n"
content .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;`n"
content .= "`n"
content .= "CREATE TABLE IF NOT EXISTS jobs (`n"
content .= "  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,`n"
content .= "  client_id BIGINT UNSIGNED NOT NULL,`n"
content .= "  category_id BIGINT UNSIGNED NOT NULL,`n"
content .= "  title VARCHAR(180) NOT NULL,`n"
content .= "  description_html MEDIUMTEXT NOT NULL,`n"
content .= "  budget_type ENUM('fixed','hourly') NOT NULL,`n"
content .= "  status ENUM('draft','published','in_progress','completed','canceled') NOT NULL DEFAULT 'draft',`n"
content .= "  published_at DATETIME NULL,`n"
content .= "  created_at DATETIME NOT NULL,`n"
content .= "  KEY ix_jobs_status_pub (status,published_at),`n"
content .= "  CONSTRAINT fk_jobs_client FOREIGN KEY (client_id) REFERENCES users(id),`n"
content .= "  CONSTRAINT fk_jobs_cat FOREIGN KEY (category_id) REFERENCES job_categories(id)`n"
content .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;`n"
WriteFile("database\migrations\001_schema.sql", content)

content := ""
content .= "INSERT INTO job_categories(name,slug) VALUES`n"
content .= "('Design','design'),`n"
content .= "('Desenvolvimento','dev'),`n"
content .= "('Marketing','marketing')`n"
content .= "ON DUPLICATE KEY UPDATE name=VALUES(name);`n"
WriteFile("database\seeds\001_seed.sql", content)

; =========================
; scripts/migrate.php
; =========================
content := ""
content .= "<?php`n"
content .= "declare(strict_types=1);`n"
content .= "`n"
content .= "require __DIR__ . '/../src/bootstrap.php';`n"
content .= "`n"
content .= "use App\\DB;`n"
content .= "use App\\Helpers\\Env;`n"
content .= "`n"
content .= "$pdo = DB::pdo();`n"
content .= "$files = glob(__DIR__ . '/../database/migrations/*.sql');`n"
content .= "sort($files);`n"
content .= "foreach ($files as $f) {`n"
content .= "  echo ""Applying: $f\n"";`n"
content .= "  $pdo->exec((string)file_get_contents($f));`n"
content .= "}`n"
content .= "$seed = __DIR__ . '/../database/seeds/001_seed.sql';`n"
content .= "if (file_exists($seed)) {`n"
content .= "  echo ""Seeding: $seed\n"";`n"
content .= "  $pdo->exec((string)file_get_contents($seed));`n"
content .= "}`n"
content .= "$adminEmail = (string)Env::get('ADMIN_EMAIL', 'admin@local.test');`n"
content .= "$adminPass  = (string)Env::get('ADMIN_PASS', '123456');`n"
content .= "$st = $pdo->prepare(""SELECT id FROM users WHERE email=? LIMIT 1"");`n"
content .= "$st->execute([$adminEmail]);`n"
content .= "$exists = $st->fetch();`n"
content .= "if (!$exists) {`n"
content .= "  $hash = password_hash($adminPass, PASSWORD_DEFAULT);`n"
content .= "  $pdo->prepare(""INSERT INTO users(role,name,email,password_hash,status,created_at,updated_at)`n"
content .= "                 VALUES('admin','Admin',?,?, 'active', NOW(), NOW())"")`n"
content .= "      ->execute([$adminEmail, $hash]);`n"
content .= "  echo ""Admin created: {$adminEmail} / {$adminPass}\n"";`n"
content .= "}`n"
content .= "echo ""Done.\n"";`n"
WriteFile("scripts\migrate.php", content)

; =========================
; extras
; =========================
WriteFile("storage\logs\.gitkeep", "")
content := ""
content .= "# FreelizzV2 MVP (base)`n`n"
content .= "## Subir`n"
content .= "1) docker compose up -d --build`n"
content .= "2) docker exec -it freelizz_web php /var/www/html/scripts/migrate.php`n"
content .= "3) http://localhost:8787`n`n"
content .= "## MySQL (host)`n"
content .= "porta: 3390`n"
content .= "db: freelizz`n"
content .= "user: freelizz`n"
content .= "pass: freelizz123`n"
WriteFile("README.md", content)

MsgBox, 64, OK, Estrutura criada em:`n%base%`n`nPassos:`n1) docker compose up -d --build`n2) docker exec -it freelizz_web php /var/www/html/scripts/migrate.php`n3) http://localhost:8787
ExitApp
