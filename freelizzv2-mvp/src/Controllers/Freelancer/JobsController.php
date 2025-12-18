<?php
declare(strict_types=1);

namespace App\Controllers\Freelancer;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Plan;
use App\Helpers\Flash;
use App\Helpers\Csrf;
use App\Helpers\Sanitizer;

final class JobsController extends BaseController {
  public function __construct() { Auth::requireFreelancer(); }

  public function index(): void {
    $pdo = DB::pdo();
    $cats = $pdo->query("SELECT id,name FROM job_categories ORDER BY name ASC")->fetchAll();

    $cat = (int)($_GET['cat'] ?? 0);
    $type = (string)($_GET['type'] ?? '');

    $where = ["j.status='published'"];
    $params = [];

    if ($cat > 0) { $where[] = "j.category_id=?"; $params[] = $cat; }
    if (in_array($type, ['fixed','hourly'], true)) { $where[] = "j.budget_type=?"; $params[] = $type; }

    $sql = "SELECT j.id,j.title,j.budget_type,j.budget_min_cents,j.budget_max_cents,j.hourly_rate_cents,j.published_at,c.name category_name
            FROM jobs j JOIN job_categories c ON c.id=j.category_id
            WHERE " . implode(" AND ", $where) . "
            ORDER BY j.published_at DESC LIMIT 200";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $jobs = $st->fetchAll();

    $this->view('freelancer/jobs/index', compact('jobs','cats','cat','type'));
  }

  public function show(array $p): void {
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();
    $fid = Auth::id();

    $st = $pdo->prepare("SELECT j.*, c.name category_name, u.name client_name
                         FROM jobs j
                         JOIN job_categories c ON c.id=j.category_id
                         JOIN users u ON u.id=j.client_id
                         WHERE j.id=? AND j.status='published' LIMIT 1");
    $st->execute([$id]);
    $job = $st->fetch();
    if (!$job) { Flash::set('error','Job não encontrado.'); header('Location:/freelancer/jobs'); exit; }

    $plan = Plan::resolveForFreelancer($fid);

    // proposta já enviada?
    $st2 = $pdo->prepare("SELECT * FROM proposals WHERE job_id=? AND freelancer_id=? LIMIT 1");
    $st2->execute([$id,$fid]);
    $myProposal = $st2->fetch();

    // média das propostas (Free não vê)
    $avg = null;
    if ((int)($plan['can_view_avg'] ?? 0) === 1) {
      $st3 = $pdo->prepare("SELECT AVG(price_cents) FROM proposals WHERE job_id=? AND status='sent'");
      $st3->execute([$id]);
      $avg = (int)$st3->fetchColumn();
    }

    $this->view('freelancer/jobs/show', compact('job','plan','myProposal','avg'));
  }

  public function propose(array $p): void {
    Csrf::check();
    $jobId = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();
    $fid = Auth::id();

    $plan = Plan::resolveForFreelancer($fid);
    if (!Plan::canSendProposal($fid, $plan)) {
      Flash::set('error','Limite diário de propostas atingido para seu plano.');
      $this->redirect('/freelancer/jobs/'.$jobId);
    }

    $cover = Sanitizer::html((string)($_POST['cover_letter_html'] ?? ''));
    $price = (int)($_POST['price_cents'] ?? 0);
    $eta = (int)($_POST['eta_days'] ?? 0);

    if ($cover === '' || $price <= 0 || $eta <= 0) {
      Flash::set('error','Preencha proposta, valor e prazo.');
      $this->redirect('/freelancer/jobs/'.$jobId);
    }

    $st = $pdo->prepare("SELECT id FROM jobs WHERE id=? AND status='published' LIMIT 1");
    $st->execute([$jobId]);
    if (!$st->fetch()) { Flash::set('error','Job inválido.'); $this->redirect('/freelancer/jobs'); }

    try {
      $pdo->prepare("INSERT INTO proposals(job_id,freelancer_id,cover_letter_html,price_cents,eta_days,status,plan_code_snapshot,plan_weight_snapshot,created_at,updated_at)
                     VALUES(?,?,?,?,?,'sent',?,?,NOW(),NOW())")
          ->execute([
            $jobId,$fid,$cover,$price,$eta,
            (string)$plan['code'], (int)$plan['proposal_sort_weight']
          ]);
      Flash::set('ok','Proposta enviada.');
    } catch (\Throwable $e) {
      Flash::set('error','Você já enviou proposta para este job.');
    }

    $this->redirect('/freelancer/jobs/'.$jobId);
  }
}
