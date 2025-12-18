<?php
declare(strict_types=1);

namespace App\Helpers;

final class Sanitizer {
  public static function html(string $html): string {
    $html = trim($html);
    if ($html === '') return '';

    // Remove tags perigosas direto
    $html = preg_replace('#<(script|style|iframe|object|embed)[^>]*>.*?</\1>#is', '', $html) ?? '';

    $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><h1><h2><h3><h4><blockquote><a><span><div>';
    $html = strip_tags($html, $allowed);

    // Limpa atributos perigosos (DOMDocument)
    $dom = new \DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $safeAttrs = ['href','target','rel','title','alt'];
    $nodes = $dom->getElementsByTagName('*');

    foreach ($nodes as $node) {
      if (!$node->hasAttributes()) continue;

      $toRemove = [];
      foreach ($node->attributes as $attr) {
        $name = strtolower($attr->name);
        $val  = (string)$attr->value;

        if (!in_array($name, $safeAttrs, true)) $toRemove[] = $attr->name;

        if ($name === 'href') {
          $v = trim($val);
          if (preg_match('#^\s*javascript:#i', $v)) $toRemove[] = $attr->name;
        }
      }
      foreach ($toRemove as $a) $node->removeAttribute($a);

      // Se for <a>, força rel seguro quando target=_blank
      if (strtolower($node->nodeName) === 'a') {
        if (strtolower((string)$node->getAttribute('target')) === '_blank') {
          $node->setAttribute('rel', 'noopener noreferrer');
        }
      }
    }

    $out = $dom->saveHTML();
    return is_string($out) ? $out : '';
  }
}
