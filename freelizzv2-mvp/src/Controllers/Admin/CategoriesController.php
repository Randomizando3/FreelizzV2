<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;

final class CategoriesController extends BaseController {
  public function __construct() {
    Auth::requireAdmin();
    $this->layout = 'layouts/admin';
  }

  public function index(): void {
    $cats = DB::pdo()->query("SELECT id,name,slug FROM job_categories ORDER BY name ASC")->fetchAll();
    $this->view('admin/categories/index', ['cats'=>$cats]);
  }

  public function create(): void {
    Csrf::check();
    $name = trim((string)($_POST['name'] ?? ''));
    $slug = trim((string)($_POST['slug'] ?? ''));
    if ($name === '' || $slug === '') { Flash::set('error','Nome/slug obrigatórios.'); $this->redirect('/admin/categories'); }

    try {
      DB::pdo()->prepare("INSERT INTO job_categories(name,slug) VALUES(?,?)")->execute([$name,$slug]);
      Flash::set('ok','Categoria criada.');
    } catch (\Throwable $e) {
      Flash::set('error','Erro ao criar (slug duplicado?).');
    }
    $this->redirect('/admin/categories');
  }

  public function update(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $name = trim((string)($_POST['name'] ?? ''));
    $slug = trim((string)($_POST['slug'] ?? ''));
    if ($name === '' || $slug === '') { Flash::set('error','Nome/slug obrigatórios.'); $this->redirect('/admin/categories'); }

    try {
      DB::pdo()->prepare("UPDATE job_categories SET name=?, slug=? WHERE id=?")->execute([$name,$slug,$id]);
      Flash::set('ok','Categoria atualizada.');
    } catch (\Throwable $e) {
      Flash::set('error','Erro ao atualizar (slug duplicado?).');
    }
    $this->redirect('/admin/categories');
  }

  public function delete(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);

    $pdo = DB::pdo();
    $cnt = (int)$pdo->prepare("SELECT COUNT(*) FROM jobs WHERE category_id=?")
                    ->execute([$id]) ?: 0;

    try {
      $pdo->prepare("DELETE FROM job_categories WHERE id=?")->execute([$id]);
      Flash::set('ok','Categoria removida.');
    } catch (\Throwable $e) {
      Flash::set('error','Não foi possível remover (em uso?).');
    }

    $this->redirect('/admin/categories');
  }
}
