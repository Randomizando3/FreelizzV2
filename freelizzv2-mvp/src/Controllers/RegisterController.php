<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DB;
use App\Helpers\Csrf;
use App\Helpers\Flash;

final class RegisterController extends BaseController {
  public function form(): void { $this->view('auth/register'); }

  public function submit(): void {
    Csrf::check();
    $pdo = DB::pdo();

    $role = (string)($_POST['role'] ?? '');
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $pass = (string)($_POST['password'] ?? '');

    if (!in_array($role, ['client','freelancer'], true) || $name==='' || $email==='' || strlen($pass) < 6) {
      Flash::set('error','Dados inválidos (senha mínimo 6).');
      $this->redirect('/register');
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    try {
      $pdo->prepare("INSERT INTO users(role,name,email,password_hash,status,created_at,updated_at)
                     VALUES(?,?,?,?, 'active', NOW(), NOW())")
          ->execute([$role,$name,$email,$hash]);
      $uid = (int)$pdo->lastInsertId();

      if ($role === 'freelancer') {
        $pdo->prepare("INSERT INTO freelancer_accounts(user_id,plan_code,created_at,updated_at)
                       VALUES(?, 'free', NOW(), NOW())
                       ON DUPLICATE KEY UPDATE updated_at=NOW()")
            ->execute([$uid]);

        // cria assinatura free ativa (sem expiração) para simplificar MVP
        $pdo->prepare("INSERT INTO subscriptions(user_id,plan_code,status,started_at,ends_at,created_at,updated_at)
                       VALUES(?, 'free','active', NOW(), NULL, NOW(), NOW())")
            ->execute([$uid]);
      }

      $_SESSION['user'] = ['id'=>$uid,'role'=>$role,'name'=>$name,'email'=>$email];

      Flash::set('ok','Conta criada.');
      $this->redirect($role === 'client' ? '/client' : '/freelancer');
    } catch (\Throwable $e) {
      Flash::set('error','Erro ao cadastrar (email já existe?).');
      $this->redirect('/register');
    }
  }
}
