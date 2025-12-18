<?php
declare(strict_types=1);

namespace App;

final class Router {
  private array $routes = [];

  public function get(string $path, string $handler): void { $this->add('GET', $path, $handler); }
  public function post(string $path, string $handler): void { $this->add('POST', $path, $handler); }

  private function add(string $method, string $path, string $handler): void {
    $pattern = preg_replace('#\\{([a-zA-Z_][a-zA-Z0-9_]*)\\}#', '(?P<$1>[^/]+)', $path);
    $pattern = '#^' . rtrim($pattern, '/') . '/?$#';
    $this->routes[] = ['method'=>$method,'pattern'=>$pattern,'handler'=>$handler];
  }

  public function dispatch(): void {
    $uri = (string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    foreach ($this->routes as $r) {
      if ($r['method'] !== $method) continue;
      if (!preg_match($r['pattern'], $uri, $m)) continue;

      $params = [];
      foreach ($m as $k => $v) {
        if (!is_int($k)) $params[$k] = $v;
      }

      $this->run($r['handler'], $params);
      return; // encerra o dispatch após a primeira rota casada
    }

    http_response_code(404);
    echo '<h1>404</h1>';
  }

  private function run(string $handler, array $params): void {
    [$class, $method] = explode('@', $handler, 2);
    $obj = new $class();
    $obj->$method($params);
  }
}
