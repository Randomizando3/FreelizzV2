<?php
declare(strict_types=1);

namespace App\Controllers\Freelancer;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Sanitizer;
use App\Helpers\Upload;

final class ProfileController extends BaseController {
  public function __construct() { Auth::requireFreelancer(); }

  public function form(): void {
    $pdo = DB::pdo();
    $fid = Auth::id();
    $st = $pdo->prepare("SELECT * FROM freelancer_accounts WHERE user_id=? LIMIT 1");
    $st->execute([$fid]);
    $acc = $st->fetch();
    $this->view('freelancer/profile', ['acc'=>$acc]);
  }

  public function save(): void {
    Csrf::check();
    $pdo = DB::pdo();
    $fid = Auth::id();

    $area = trim((string)($_POST['area'] ?? ''));
    $bio = Sanitizer::html((string)($_POST['bio_html'] ?? ''));
    $portUrl = trim((string)($_POST['portfolio_url'] ?? ''));
    $portHtml = Sanitizer::html((string)($_POST['portfolio_html'] ?? ''));

    $avatarPath = null;
    if (!empty($_FILES['avatar']['name'])) {
      $avatarPath = Upload::save($_FILES['avatar'], ['subdir'=>'avatars','max_bytes'=>2_000_000]);
    }

    $pdo->prepare(
      "INSERT INTO freelancer_accounts(user_id,plan_code,avatar_path,area,bio_html,portfolio_url,portfolio_html,created_at,updated_at)
       VALUES(?, 'free', ?,?,?,?,?, NOW(), NOW())
       ON DUPLICATE KEY UPDATE
         avatar_path=COALESCE(VALUES(avatar_path), avatar_path),
         area=VALUES(area), bio_html=VALUES(bio_html),
         portfolio_url=VALUES(portfolio_url), portfolio_html=VALUES(portfolio_html),
         updated_at=NOW()"
    )->execute([$fid,$avatarPath,$area,$bio,$portUrl,$portHtml]);

    Flash::set('ok','Perfil atualizado.');
    $this->redirect('/freelancer/profile');
  }
}
