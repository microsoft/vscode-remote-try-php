<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Preprocessors;

if (!defined('ABSPATH')) exit;


interface Preprocessor {
  /**
   * @param array{contentSize: string} $layout
   * @param array{spacing: array{padding: array{bottom: string, left: string, right: string, top: string}, blockGap: string}} $styles
   */
  public function preprocess(array $parsedBlocks, array $layout, array $styles): array;
}
