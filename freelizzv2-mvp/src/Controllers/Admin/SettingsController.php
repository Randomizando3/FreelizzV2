<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DB;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Settings;

final class SettingsController extends BaseController {
  public function __construct() {
    Auth::requireAdmin();
    $this->layout = 'layouts/admin';
  }

  public function index(): void {
    $pdo = DB::pdo();
    $settings = Settings::all();
    $plans = $pdo->query("SELECT * FROM plans ORDER BY FIELD(code,'free','plus','premium')")->fetchAll();
    $this->view('admin/settings/index', compact('settings','plans'));
  }

  public function save(): void {
    Csrf::check();
    $adminId = Auth::id();

    $keys = [
      'support_email',
      'support_whatsapp',
      'contact_to_email',
      'verification_price_cents',
      'mp_access_token',
      'mp_webhook_secret',
    ];

    foreach ($keys as $k) {
      $v = trim((string)($_POST[$k] ?? ''));
      Settings::set($k, $v, $adminId);
    }

    Flash::set('ok','Configurações salvas.');
    $this->redirect('/admin/settings');
  }

  public function savePlan(array $p): void {
    Csrf::check();
    $code = (string)($p['code'] ?? '');
    if (!in_array($code, ['free','plus','premium'], true)) {
      Flash::set('error','Plano inválido.');
      $this->redirect('/admin/settings');
    }

    $pdo = DB::pdo();

    $name = trim((string)($_POST['name'] ?? ''));
    $proposals = (int)($_POST['proposals_per_day'] ?? 0);
    $featured = (int)($_POST['featured_public'] ?? 0) ? 1 : 0;
    $take = (float)($_POST['take_rate_pct'] ?? 0);
    $withdrawMin = (int)($_POST['withdraw_min_cents'] ?? 0);
    $withdrawWindow = trim((string)($_POST['withdraw_window'] ?? ''));
    $withdrawSpeed = trim((string)($_POST['withdraw_speed'] ?? ''));
    $canViewAvg = (int)($_POST['can_view_avg'] ?? 0) ? 1 : 0;
    $support = (int)($_POST['support_priority'] ?? 1);

    $pdo->prepare(
      "UPDATE plans
       SET name=?, proposals_per_day=?, featured_public=?, take_rate_pct=?,
           withdraw_min_cents=?, withdraw_window=?, withdraw_speed=?,
           can_view_avg=?, support_priority=?, updated_at=NOW()
       WHERE code=?"
    )->execute([$name,$proposals,$featured,$take,$withdrawMin,$withdrawWindow,$withdrawSpeed,$canViewAvg,$support,$code]);

    Flash::set('ok','Plano atualizado.');
    $this->redirect('/admin/settings');
  }
}
