<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DB;

final class PublicController extends BaseController {
  public function home(): void { $this->view('public/home'); }

  public function jobs(): void {
    $pdo = DB::pdo();

    $sql = "SELECT j.id,j.title,j.budget_type,j.published_at,c.name AS category_name
            FROM jobs j
            JOIN job_categories c ON c.id=j.category_id
            WHERE j.status='published'
            ORDER BY j.published_at DESC
            LIMIT 50";

    $st = $pdo->query($sql);
    $jobs = $st->fetchAll();

    $this->view('public/jobs', ['jobs'=>$jobs]);
  }

  public function jobShow(array $p): void {
    $id = (int)($p['id'] ?? 0);
    $pdo = DB::pdo();

    $st = $pdo->prepare("SELECT j.*, c.name AS category_name
                         FROM jobs j
                         JOIN job_categories c ON c.id=j.category_id
                         WHERE j.id=?");
    $st->execute([$id]);

    $job = $st->fetch();
    if (!$job) { http_response_code(404); echo '<h1>Job nao encontrado</h1>'; return; }

    $this->view('public/job_show', ['job'=>$job]);
  }
}
