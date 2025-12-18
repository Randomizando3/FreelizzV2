<?php
declare(strict_types=1);

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Sanitizer;

final class JobsController extends BaseController {
  public function __construct() { Auth::requireClient(); }

  public function index(): void {
    $pdo = DB::pdo();
    $cid = Auth::id();

    $st = $pdo->prepare("SELECT j.*, c.name AS category_name
                         FROM jobs j JOIN job_categories c ON c.id=j.category_id
                         WHERE j.client_id=?
                         ORDER BY j.id DESC LIMIT 200");
    $st->execute([$cid]);
    $jobs = $st->fetchAll();

    $cats = $pdo->query("SELECT id,name FROM job_categories ORDER BY name ASC")->fetchAll();

    $this->view('client/jobs/index', compact('jobs','cats'));
  }

  public function create(): void {
    Csrf::check();
    $pdo = DB::pdo();
    $cid = Auth::id();

    $title = trim((string)($_POST['title'] ?? ''));
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $budgetType = (string)($_POST['budget_type'] ?? 'fixed');
    $desc = Sanitizer::html((string)($_POST['description_html'] ?? ''));

    $budgetMin = (int)($_POST['budget_min_cents'] ?? 0);
    $budgetMax = (int)($_POST['budget_max_cents'] ?? 0);
    $hourly = (int)($_POST['hourly_rate_cents'] ?? 0);

    if ($title==='' || $categoryId<=0 || !in_array($budgetType,['fixed','hourly'],true)) {
      Flash::set('error','Dados inválidos.');
      $this->redirect('/client/jobs');
    }

    $status = 'published';
    $pdo->prepare("INSERT INTO jobs(client_id,category_id,title,description_html,budget_type,budget_min_cents,budget_max_cents,hourly_rate_cents,status,published_at,created_at,updated_at)
                   VALUES(?,?,?,?,?,?,?,?, 'published', NOW(), NOW(), NOW())")
        ->execute([$cid,$categoryId,$title,$desc,$budgetType,$budgetMin,$budgetMax,$hourly]);

    Flash::set('ok','Job publicado.');
    $this->redirect('/client/jobs');
  }

  public function show(array $p): void {
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();
    $cid = Auth::id();

    $st = $pdo->prepare("SELECT j.*, c.name category_name
                         FROM jobs j JOIN job_categories c ON c.id=j.category_id
                         WHERE j.id=? AND j.client_id=? LIMIT 1");
    $st->execute([$id,$cid]);
    $job = $st->fetch();
    if (!$job) { Flash::set('error','Job não encontrado.'); $this->redirect('/client/jobs'); }

    // propostas ordenadas por plano snapshot
    $st2 = $pdo->prepare(
      "SELECT p.*, u.name freelancer_name, u.email freelancer_email
       FROM proposals p
       JOIN users u ON u.id=p.freelancer_id
       WHERE p.job_id=?
       ORDER BY p.plan_weight_snapshot DESC, p.created_at DESC"
    );
    $st2->execute([$id]);
    $proposals = $st2->fetchAll();

    $this->view('client/jobs/show', compact('job','proposals'));
  }

  public function accept(array $p): void {
    Csrf::check();
    $jobId = (int)($p['id'] ?? 0);
    $proposalId = (int)($_POST['proposal_id'] ?? 0);

    $pdo = DB::pdo();
    $cid = Auth::id();

    $pdo->beginTransaction();
    try {
      $st = $pdo->prepare("SELECT * FROM jobs WHERE id=? AND client_id=? FOR UPDATE");
      $st->execute([$jobId,$cid]);
      $job = $st->fetch();
      if (!$job || $job['status'] !== 'published') throw new \RuntimeException('Job inválido.');

      $st2 = $pdo->prepare("SELECT * FROM proposals WHERE id=? AND job_id=? FOR UPDATE");
      $st2->execute([$proposalId,$jobId]);
      $prop = $st2->fetch();
      if (!$prop || $prop['status'] !== 'sent') throw new \RuntimeException('Proposta inválida.');

      // rejeita outras propostas
      $pdo->prepare("UPDATE proposals SET status='rejected', updated_at=NOW() WHERE job_id=? AND id<>? AND status='sent'")
          ->execute([$jobId,$proposalId]);

      // aceita a escolhida
      $pdo->prepare("UPDATE proposals SET status='accepted', updated_at=NOW() WHERE id=?")->execute([$proposalId]);

      // cria projeto
      $pdo->prepare("INSERT INTO projects(job_id,client_id,freelancer_id,proposal_id,status,created_at,updated_at)
                     VALUES(?,?,?,?, 'active', NOW(), NOW())")
          ->execute([$jobId,$cid,(int)$prop['freelancer_id'],$proposalId]);

      $pdo->prepare("UPDATE jobs SET status='in_progress', updated_at=NOW() WHERE id=?")->execute([$jobId]);

      $pdo->commit();
      Flash::set('ok','Freelancer aceito. Projeto criado.');
      $this->redirect('/client/projects');
    } catch (\Throwable $e) {
      $pdo->rollBack();
      Flash::set('error','Falha ao aceitar: '.$e->getMessage());
      $this->redirect('/client/jobs/'.$jobId);
    }
  }
}
