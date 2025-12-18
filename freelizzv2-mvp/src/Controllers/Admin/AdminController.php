<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;

final class AdminController extends BaseController {
  public function __construct() {
    Auth::requireAdmin();
    $this->layout = 'layouts/admin';
  }

  public function dashboard(): void {
    $pdo = DB::pdo();

    $usersTotal = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $clients    = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
    $freelas    = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='freelancer'")->fetchColumn();

    $jobsPub    = (int)$pdo->query("SELECT COUNT(*) FROM jobs WHERE status='published'")->fetchColumn();
    $jobsAll    = (int)$pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();

    $pendingVer = (int)$pdo->query("SELECT COUNT(*) FROM freelancer_verifications WHERE status='pending'")->fetchColumn();

    $this->view('admin/dashboard', compact('usersTotal','clients','freelas','jobsPub','jobsAll','pendingVer'));
  }
}
