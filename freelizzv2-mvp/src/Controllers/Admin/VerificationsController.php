<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;

final class VerificationsController extends BaseController {
  public function __construct() {
    Auth::requireAdmin();
    $this->layout = 'layouts/admin';
  }

  public function index(): void {
    $pdo = DB::pdo();
    $status = (string)($_GET['status'] ?? 'pending');
    if (!in_array($status, ['pending','approved','rejected'], true)) $status = 'pending';

    $st = $pdo->prepare(
      "SELECT v.id,v.user_id,v.status,v.created_at,u.email,u.name
       FROM freelancer_verifications v
       JOIN users u ON u.id=v.user_id
       WHERE v.status=?
       ORDER BY v.id DESC
       LIMIT 200"
    );
    $st->execute([$status]);
    $rows = $st->fetchAll();

    $this->view('admin/verifications/index', compact('rows','status'));
  }

  public function show(array $p): void {
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();

    $st = $pdo->prepare(
      "SELECT v.*, u.email, u.name
       FROM freelancer_verifications v
       JOIN users u ON u.id=v.user_id
       WHERE v.id=?"
    );
    $st->execute([$id]);
    $ver = $st->fetch();
    if (!$ver) { Flash::set('error','Solicitação não encontrada.'); $this->redirect('/admin/verifications'); }

    $this->view('admin/verifications/show', ['ver'=>$ver]);
  }

  public function approve(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();

    $pdo->prepare("UPDATE freelancer_verifications SET status='approved', reviewed_at=NOW(), reviewed_by=? WHERE id=?")
        ->execute([Auth::id(), $id]);

    Flash::set('ok','Verificação aprovada.');
    $this->redirect('/admin/verifications/'.$id);
  }

  public function reject(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $reason = trim((string)($_POST['reason'] ?? ''));
    if ($reason === '') $reason = 'Documento ilegível ou selfie inválida.';

    $pdo = DB::pdo();
    $pdo->prepare("UPDATE freelancer_verifications SET status='rejected', reason=?, reviewed_at=NOW(), reviewed_by=? WHERE id=?")
        ->execute([$reason, Auth::id(), $id]);

    Flash::set('ok','Verificação reprovada.');
    $this->redirect('/admin/verifications/'.$id);
  }
}
