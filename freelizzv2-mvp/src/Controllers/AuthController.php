<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DB;
use App\Helpers\Csrf;

final class AuthController extends BaseController {
  public function loginForm(): void { $this->view('auth/login'); }

  public function login(): void {
    Csrf::check();

    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $pass  = (string)($_POST['password'] ?? '');

    $pdo = DB::pdo();
    $st = $pdo->prepare("SELECT id,role,name,email,password_hash,status FROM users WHERE email=? LIMIT 1");
    $st->execute([$email]);
    $u = $st->fetch();

    if (!$u || $u['status'] !== 'active' || !password_verify($pass, (string)$u['password_hash'])) {
      $this->redirect('/login?err=1');
    }

    $_SESSION['user'] = ['id'=>$u['id'],'role'=>$u['role'],'name'=>$u['name'],'email'=>$u['email']];
    if ($u['role'] === 'admin') $this->redirect('/admin');
    $this->redirect('/');
  }

  public function logout(): void {
    Csrf::check();
    session_destroy();
    $this->redirect('/');
  }
}
