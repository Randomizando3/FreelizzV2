<?php
declare(strict_types=1);

namespace App\Controllers\Freelancer;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Settings;
use App\Helpers\Upload;

final class VerificationController extends BaseController {
  public function __construct() { Auth::requireFreelancer(); }

  public function index(): void {
    $pdo = DB::pdo();
    $fid = Auth::id();

    $st = $pdo->prepare("SELECT * FROM freelancer_verifications WHERE user_id=? ORDER BY id DESC LIMIT 1");
    $st->execute([$fid]);
    $ver = $st->fetch();

    $price = (int)Settings::get('verification_price_cents', '0');

    $this->view('freelancer/verification', compact('ver','price'));
  }

  public function submit(): void {
    Csrf::check();
    $pdo = DB::pdo();
    $fid = Auth::id();

    if (empty($_FILES['selfie']['name']) || empty($_FILES['document']['name'])) {
      Flash::set('error','Envie selfie e documento.');
      $this->redirect('/freelancer/verification');
    }

    $selfie = Upload::save($_FILES['selfie'], ['subdir'=>'verification/selfie','max_bytes'=>5_000_000]);
    $doc = Upload::save($_FILES['document'], ['subdir'=>'verification/document','max_bytes'=>5_000_000]);

    $price = (int)\App\Helpers\Settings::get('verification_price_cents', '0');

    $pdo->prepare("INSERT INTO freelancer_verifications(user_id,selfie_path,document_path,status,price_cents,paid_status,created_at)
                   VALUES(?,?,?,'pending',?, 'unpaid', NOW())")
        ->execute([$fid,$selfie,$doc,$price]);

    Flash::set('ok','Solicitação enviada. Pague a verificação para prosseguir (se houver cobrança).');
    $this->redirect('/freelancer/verification');
  }
}
