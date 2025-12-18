<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;

final class UsersController extends BaseController {
  public function __construct() {
    Auth::requireAdmin();
    $this->layout = 'layouts/admin';
  }

  public function index(): void {
    $pdo = DB::pdo();
    $role = (string)($_GET['role'] ?? '');
    $status = (string)($_GET['status'] ?? '');
    $q = trim((string)($_GET['q'] ?? ''));

    $where = [];
    $params = [];

    if (in_array($role, ['admin','client','freelancer'], true)) { $where[] = "role=?"; $params[] = $role; }
    if (in_array($status, ['active','blocked'], true)) { $where[] = "status=?"; $params[] = $status; }
    if ($q !== '') { $where[] = "(name LIKE ? OR email LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }

    $sql = "SELECT id,role,name,email,status,created_at FROM users";
    if ($where) $sql .= " WHERE " . implode(" AND ", $where);
    $sql .= " ORDER BY id DESC LIMIT 200";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $users = $st->fetchAll();

    // plano do freelancer (se existir)
    $plans = [];
    $rows = $pdo->query("SELECT user_id, plan_code FROM freelancer_accounts")->fetchAll();
    foreach ($rows as $r) $plans[(int)$r['user_id']] = (string)$r['plan_code'];

    $this->view('admin/users/index', compact('users','role','status','q','plans'));
  }

  public function createForm(): void {
    $this->view('admin/users/form', ['mode'=>'create','user'=>null,'account'=>null]);
  }

  public function create(): void {
    Csrf::check();
    $pdo = DB::pdo();

    $role = (string)($_POST['role'] ?? '');
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $pass = (string)($_POST['password'] ?? '');
    $status = (string)($_POST['status'] ?? 'active');

    if (!in_array($role, ['admin','client','freelancer'], true) || $name === '' || $email === '' || strlen($pass) < 6) {
      Flash::set('error', 'Dados inválidos (role, nome, email, senha>=6).');
      $this->redirect('/admin/users/create');
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    try {
      $pdo->prepare("INSERT INTO users(role,name,email,password_hash,status,created_at,updated_at)
                     VALUES(?,?,?,?,?,NOW(),NOW())")
          ->execute([$role,$name,$email,$hash, in_array($status,['active','blocked'],true)?$status:'active']);
      $uid = (int)$pdo->lastInsertId();

      if ($role === 'freelancer') {
        $plan = (string)($_POST['plan_code'] ?? 'free');
        if (!in_array($plan, ['free','plus','premium'], true)) $plan = 'free';
        $pdo->prepare("INSERT INTO freelancer_accounts(user_id,plan_code,created_at,updated_at)
                       VALUES(?,?,NOW(),NOW())
                       ON DUPLICATE KEY UPDATE plan_code=VALUES(plan_code), updated_at=NOW()")
            ->execute([$uid, $plan]);
      }

      Flash::set('ok', 'Usuário criado.');
      $this->redirect('/admin/users');
    } catch (\Throwable $e) {
      Flash::set('error', 'Erro ao criar usuário (email já existe?).');
      $this->redirect('/admin/users/create');
    }
  }

  public function editForm(array $p): void {
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();

    $st = $pdo->prepare("SELECT id,role,name,email,status FROM users WHERE id=?");
    $st->execute([$id]);
    $user = $st->fetch();
    if (!$user) { Flash::set('error','Usuário não encontrado.'); $this->redirect('/admin/users'); }

    $acc = null;
    if ($user['role'] === 'freelancer') {
      $st2 = $pdo->prepare("SELECT user_id,plan_code FROM freelancer_accounts WHERE user_id=?");
      $st2->execute([$id]);
      $acc = $st2->fetch();
    }

    $this->view('admin/users/form', ['mode'=>'edit','user'=>$user,'account'=>$acc]);
  }

  public function update(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();

    $role = (string)($_POST['role'] ?? '');
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $status = (string)($_POST['status'] ?? 'active');
    $newPass = (string)($_POST['new_password'] ?? '');

    if (!in_array($role, ['admin','client','freelancer'], true) || $name === '' || $email === '') {
      Flash::set('error','Dados inválidos.');
      $this->redirect('/admin/users/'.$id.'/edit');
    }

    $pdo->prepare("UPDATE users SET role=?, name=?, email=?, status=?, updated_at=NOW() WHERE id=?")
        ->execute([$role,$name,$email, in_array($status,['active','blocked'],true)?$status:'active', $id]);

    if ($newPass !== '') {
      if (strlen($newPass) < 6) {
        Flash::set('error','Senha nova deve ter 6+ caracteres.');
        $this->redirect('/admin/users/'.$id.'/edit');
      }
      $hash = password_hash($newPass, PASSWORD_DEFAULT);
      $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([$hash, $id]);
    }

    if ($role === 'freelancer') {
      $plan = (string)($_POST['plan_code'] ?? 'free');
      if (!in_array($plan, ['free','plus','premium'], true)) $plan = 'free';
      $pdo->prepare("INSERT INTO freelancer_accounts(user_id,plan_code,created_at,updated_at)
                     VALUES(?,?,NOW(),NOW())
                     ON DUPLICATE KEY UPDATE plan_code=VALUES(plan_code), updated_at=NOW()")
          ->execute([$id, $plan]);
    }

    Flash::set('ok','Usuário atualizado.');
    $this->redirect('/admin/users');
  }

  public function block(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    DB::pdo()->prepare("UPDATE users SET status='blocked', updated_at=NOW() WHERE id=?")->execute([$id]);
    Flash::set('ok','Usuário bloqueado.');
    $this->redirect('/admin/users');
  }

  public function unblock(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    DB::pdo()->prepare("UPDATE users SET status='active', updated_at=NOW() WHERE id=?")->execute([$id]);
    Flash::set('ok','Usuário desbloqueado.');
    $this->redirect('/admin/users');
  }

  public function impersonate(array $p): void {
    Csrf::check();
    $id = (int)($p['id'] ?? 0);
    Auth::impersonate($id);
    Flash::set('ok', 'Impersonation iniciada.');
    $this->redirect('/');
  }

  public function stopImpersonate(): void {
    Csrf::check();
    Auth::stopImpersonate();
    Flash::set('ok', 'Impersonation finalizada.');
    $this->redirect('/admin');
  }
}
