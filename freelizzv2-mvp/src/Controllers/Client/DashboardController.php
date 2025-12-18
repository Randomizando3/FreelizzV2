<?php
declare(strict_types=1);

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;

final class DashboardController extends BaseController {
  public function __construct() { Auth::requireClient(); }

  public function index(): void {
    $pdo = DB::pdo();
    $cid = Auth::id();

    $jobs = (int)$pdo->prepare("SELECT COUNT(*) FROM jobs WHERE client_id=?")
                     ->execute([$cid]) ?: 0;

    $st = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE client_id=?");
    $st->execute([$cid]);
    $jobs = (int)$st->fetchColumn();

    $st2 = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE client_id=? AND status='active'");
    $st2->execute([$cid]);
    $activeProjects = (int)$st2->fetchColumn();

    $this->view('client/dashboard', compact('jobs','activeProjects'));
  }
}
