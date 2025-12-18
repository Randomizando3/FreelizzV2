<?php
declare(strict_types=1);

namespace App\Controllers\Freelancer;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;

final class ProposalsController extends BaseController {
  public function __construct() { Auth::requireFreelancer(); }

  public function index(): void {
    $pdo = DB::pdo();
    $fid = Auth::id();

    $st = $pdo->prepare(
      "SELECT p.*, j.title, j.status job_status
       FROM proposals p
       JOIN jobs j ON j.id=p.job_id
       WHERE p.freelancer_id=?
       ORDER BY p.id DESC LIMIT 200"
    );
    $st->execute([$fid]);
    $proposals = $st->fetchAll();

    $this->view('freelancer/proposals/index', compact('proposals'));
  }
}
