<?php
declare(strict_types=1);

namespace App\Helpers;

use App\DB;

final class Plan {
  public static function resolveForFreelancer(int $userId): array {
    $pdo = DB::pdo();

    // Auto-expira assinatura
    $pdo->prepare("UPDATE subscriptions SET status='expired', updated_at=NOW()
                   WHERE user_id=? AND status='active' AND ends_at IS NOT NULL AND ends_at < NOW()")
        ->execute([$userId]);

    $code = 'free';

    $st = $pdo->prepare("SELECT plan_code FROM subscriptions WHERE user_id=? AND status='active' ORDER BY id DESC LIMIT 1");
    $st->execute([$userId]);
    $subCode = $st->fetchColumn();
    if ($subCode) $code = (string)$subCode;
    else {
      $st2 = $pdo->prepare("SELECT plan_code FROM freelancer_accounts WHERE user_id=? LIMIT 1");
      $st2->execute([$userId]);
      $accCode = $st2->fetchColumn();
      if ($accCode) $code = (string)$accCode;
    }

    $st3 = $pdo->prepare("SELECT * FROM plans WHERE code=? LIMIT 1");
    $st3->execute([$code]);
    $plan = $st3->fetch();
    if (!$plan) {
      $st3->execute(['free']);
      $plan = $st3->fetch();
    }
    return is_array($plan) ? $plan : ['code'=>'free','name'=>'Free','proposals_per_day'=>2,'proposal_sort_weight'=>1,'can_view_avg'=>0,'take_rate_pct'=>20.00];
  }

  public static function proposalsSentToday(int $freelancerId): int {
    $pdo = DB::pdo();
    $st = $pdo->prepare("SELECT COUNT(*) FROM proposals WHERE freelancer_id=? AND DATE(created_at)=CURDATE()");
    $st->execute([$freelancerId]);
    return (int)$st->fetchColumn();
  }

  public static function canSendProposal(int $freelancerId, array $plan): bool {
    $limit = (int)($plan['proposals_per_day'] ?? 0);
    if ($limit <= 0) return true; // ilimitado
    return self::proposalsSentToday($freelancerId) < $limit;
  }
}
