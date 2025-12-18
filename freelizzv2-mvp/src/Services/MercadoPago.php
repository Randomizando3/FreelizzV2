<?php
declare(strict_types=1);

namespace App\Services;

final class MercadoPago {
  public static function createPreference(string $accessToken, array $payload): array {
    $url = 'https://api.mercadopago.com/checkout/preferences';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
      ],
      CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
      CURLOPT_TIMEOUT => 30,
    ]);

    $raw = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($raw === false) throw new \RuntimeException('MP curl erro: ' . $err);
    $json = json_decode($raw, true);

    if ($code < 200 || $code >= 300 || !is_array($json)) {
      throw new \RuntimeException('MP erro HTTP ' . $code . ': ' . $raw);
    }

    return $json;
  }
}
