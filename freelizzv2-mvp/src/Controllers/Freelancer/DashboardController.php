<?php
declare(strict_types=1);

namespace App\Controllers\Freelancer;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Plan;

final class DashboardController extends BaseController {
  public function __construct() { Auth::requireFreelancer(); }

  public function index(): void {
    $pdo = DB::pdo();
    $fid = Auth::id();

    $plan = Plan::resolveForFreelancer($fid);
    $sentToday = Plan::proposalsSentToday($fid);

    $st = $pdo->prepare("SELECT COUNT(*) FROM proposals WHERE freelancer_id=?");
    $st->execute([$fid]);
    $proposalsTotal = (int)$st->fetchColumn();

    $st2 = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE freelancer_id=? AND status='active'");
    $st2->execute([$fid]);
    $activeProjects = (int)$st2->fetchColumn();

    $this->view('freelancer/dashboard', compact('plan','sentToday','proposalsTotal','activeProjects'));
  }
}
