<?php
declare(strict_types=1);

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;

final class ProjectsController extends BaseController {
  public function __construct() { Auth::requireClient(); }

  public function index(): void {
    $pdo = DB::pdo();
    $cid = Auth::id();

    $st = $pdo->prepare(
      "SELECT pr.*, j.title, u.name freelancer_name
       FROM projects pr
       JOIN jobs j ON j.id=pr.job_id
       JOIN users u ON u.id=pr.freelancer_id
       WHERE pr.client_id=?
       ORDER BY pr.id DESC LIMIT 200"
    );
    $st->execute([$cid]);
    $projects = $st->fetchAll();

    $this->view('client/projects/index', compact('projects'));
  }

  public function show(array $p): void {
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();
    $cid = Auth::id();

    $st = $pdo->prepare(
      "SELECT pr.*, j.title, j.description_html, u.name freelancer_name, u.email freelancer_email
       FROM projects pr
       JOIN jobs j ON j.id=pr.job_id
       JOIN users u ON u.id=pr.freelancer_id
       WHERE pr.id=? AND pr.client_id=? LIMIT 1"
    );
    $st->execute([$id,$cid]);
    $project = $st->fetch();
    if (!$project) { Flash::set('error','Projeto não encontrado.'); $this->redirect('/client/projects'); }

    $msgs = $pdo->prepare("SELECT m.*, u.name sender_name FROM project_messages m JOIN users u ON u.id=m.sender_id WHERE m.project_id=? ORDER BY m.id ASC");
    $msgs->execute([$id]);
    $messages = $msgs->fetchAll();

    $pay = $pdo->prepare("SELECT * FROM payments WHERE project_id=? AND kind='escrow' ORDER BY id DESC LIMIT 1");
    $pay->execute([$id]);
    $payment = $pay->fetch();

    $this->view('client/projects/show', compact('project','messages','payment'));
  }

  public function sendMessage(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $body = trim((string)($_POST['body'] ?? ''));

    if ($body === '') { $this->redirect('/client/projects/'.$id); }

    $pdo = DB::pdo();
    $cid = Auth::id();

    $st = $pdo->prepare("SELECT id FROM projects WHERE id=? AND client_id=? LIMIT 1");
    $st->execute([$id,$cid]);
    if (!$st->fetch()) { Flash::set('error','Acesso negado.'); $this->redirect('/client/projects'); }

    $pdo->prepare("INSERT INTO project_messages(project_id,sender_id,body,created_at) VALUES(?,?,?,NOW())")
        ->execute([$id,$cid,$body]);

    $this->redirect('/client/projects/'.$id);
  }

  public function markCompleted(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();
    $cid = Auth::id();

    $pdo->prepare("UPDATE projects SET status='completed', completed_at=NOW(), updated_at=NOW() WHERE id=? AND client_id=?")
        ->execute([$id,$cid]);

    Flash::set('ok','Projeto marcado como concluído. Agora avalie o freelancer.');
    $this->redirect('/client/projects/'.$id);
  }

  public function review(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim((string)($_POST['comment'] ?? ''));

    if ($rating < 1 || $rating > 5) { Flash::set('error','Nota inválida.'); $this->redirect('/client/projects/'.$id); }

    $pdo = DB::pdo();
    $cid = Auth::id();

    $st = $pdo->prepare("SELECT freelancer_id,status FROM projects WHERE id=? AND client_id=? LIMIT 1");
    $st->execute([$id,$cid]);
    $pr = $st->fetch();
    if (!$pr || $pr['status'] !== 'completed') { Flash::set('error','Projeto não está concluído.'); $this->redirect('/client/projects/'.$id); }

    $pdo->prepare("INSERT INTO reviews(project_id,reviewer_id,reviewee_id,rating,comment,created_at)
                   VALUES(?,?,?,?,?,NOW())")
        ->execute([$id,$cid,(int)$pr['freelancer_id'],$rating,$comment]);

    Flash::set('ok','Avaliação enviada.');
    $this->redirect('/client/projects/'.$id);
  }

  public function dispute(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $reason = trim((string)($_POST['reason'] ?? ''));
    $details = trim((string)($_POST['details'] ?? ''));

    if ($reason==='' || $details==='') { Flash::set('error','Informe motivo e detalhes.'); $this->redirect('/client/projects/'.$id); }

    $pdo = DB::pdo();
    $cid = Auth::id();

    $st = $pdo->prepare("SELECT id FROM projects WHERE id=? AND client_id=? LIMIT 1");
    $st->execute([$id,$cid]);
    if (!$st->fetch()) { Flash::set('error','Acesso negado.'); $this->redirect('/client/projects'); }

    $pdo->prepare("INSERT INTO disputes(project_id,opened_by,status,reason,details,created_at,updated_at)
                   VALUES(?,?,'open',?,?,NOW(),NOW())")
        ->execute([$id,$cid,$reason,$details]);

    $pdo->prepare("UPDATE projects SET status='disputed', updated_at=NOW() WHERE id=?")->execute([$id]);

    Flash::set('ok','Denúncia/mediação aberta. Admin irá revisar.');
    $this->redirect('/client/projects/'.$id);
  }
}
