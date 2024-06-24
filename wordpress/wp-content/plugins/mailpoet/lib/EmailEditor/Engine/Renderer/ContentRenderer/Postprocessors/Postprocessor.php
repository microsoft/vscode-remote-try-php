<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Postprocessors;

if (!defined('ABSPATH')) exit;


interface Postprocessor {
  public function postprocess(string $html): string;
}
