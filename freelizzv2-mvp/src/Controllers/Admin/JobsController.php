<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Sanitizer;

final class JobsController extends BaseController {
  public function __construct() {
    Auth::requireAdmin();
    $this->layout = 'layouts/admin';
  }

  public function index(): void {
    $pdo = DB::pdo();
    $status = (string)($_GET['status'] ?? '');

    $where = [];
    $params = [];
    if (in_array($status, ['draft','published','in_progress','completed','canceled'], true)) {
      $where[] = "j.status=?";
      $params[] = $status;
    }

    $sql = "SELECT j.id,j.title,j.status,j.budget_type,j.created_at,j.published_at,
                   u.email AS client_email, c.name AS category_name
            FROM jobs j
            JOIN users u ON u.id=j.client_id
            JOIN job_categories c ON c.id=j.category_id";

    if ($where) $sql .= " WHERE " . implode(" AND ", $where);
    $sql .= " ORDER BY j.id DESC LIMIT 200";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $jobs = $st->fetchAll();

    $cats = $pdo->query("SELECT id,name FROM job_categories ORDER BY name ASC")->fetchAll();

    $this->view('admin/jobs/index', compact('jobs','status','cats'));
  }

  public function create(): void {
    Csrf::check();
    $pdo = DB::pdo();

    $clientId = (int)($_POST['client_id'] ?? 0);
    $catId = (int)($_POST['category_id'] ?? 0);
    $title = trim((string)($_POST['title'] ?? ''));
    $budgetType = (string)($_POST['budget_type'] ?? 'fixed');
    $desc = Sanitizer::html((string)($_POST['description_html'] ?? ''));
    $status = (string)($_POST['status'] ?? 'draft');

    if ($clientId <= 0 || $catId <= 0 || $title === '' || !in_array($budgetType, ['fixed','hourly'], true)) {
      Flash::set('error','Dados inválidos.');
      $this->redirect('/admin/jobs');
    }

    if (!in_array($status, ['draft','published','in_progress','completed','canceled'], true)) $status = 'draft';
    $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    $pdo->prepare("INSERT INTO jobs(client_id,category_id,title,description_html,budget_type,status,published_at,created_at)
                   VALUES(?,?,?,?,?,?,?,NOW())")
        ->execute([$clientId,$catId,$title,$desc,$budgetType,$status,$publishedAt]);

    Flash::set('ok','Job criado.');
    $this->redirect('/admin/jobs');
  }

  public function show(array $p): void {
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();
    $st = $pdo->prepare("SELECT j.*, u.email client_email, c.name category_name
                         FROM jobs j
                         JOIN users u ON u.id=j.client_id
                         JOIN job_categories c ON c.id=j.category_id
                         WHERE j.id=?");
    $st->execute([$id]);
    $job = $st->fetch();
    if (!$job) { Flash::set('error','Job não encontrado.'); $this->redirect('/admin/jobs'); }

    $this->view('admin/jobs/show', ['job'=>$job]);
  }

  public function updateStatus(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $status = (string)($_POST['status'] ?? '');

    if (!in_array($status, ['draft','published','in_progress','completed','canceled'], true)) {
      Flash::set('error','Status inválido.');
      $this->redirect('/admin/jobs/'.$id);
    }

    $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    DB::pdo()->prepare("UPDATE jobs SET status=?, published_at=COALESCE(?, published_at) WHERE id=?")
             ->execute([$status, $publishedAt, $id]);

    Flash::set('ok','Status atualizado.');
    $this->redirect('/admin/jobs/'.$id);
  }
}
