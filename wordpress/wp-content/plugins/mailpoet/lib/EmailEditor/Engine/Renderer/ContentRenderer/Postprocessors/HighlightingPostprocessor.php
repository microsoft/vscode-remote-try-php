<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Postprocessors;

if (!defined('ABSPATH')) exit;


/**
 * This postprocessor replaces <mark> tags with <span> tags because mark tags are not supported across all email clients
 */
class HighlightingPostprocessor implements Postprocessor {
  public function postprocess(string $html): string {
    return str_replace(
      ['<mark', '</mark>'],
      ['<span', '</span>'],
      $html
    );
  }
}
