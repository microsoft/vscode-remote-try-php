<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


class ApiDataSanitizer {
  /** @var NewsletterHtmlSanitizer */
  private $htmlSanitizer;

  /**
   * Configuration specifies which block types and properties within newsletters content blocks are sanitized
   */
  private const SANITIZATION_CONFIG = [
    'header' => ['text'],
    'footer' => ['text'],
    'text' => ['text'],
  ];

  public function __construct(
    NewsletterHtmlSanitizer $htmlSanitizer
  ) {
    $this->htmlSanitizer = $htmlSanitizer;
  }

  public function sanitizeBody(array $body): array {
    if (isset($body['content']) && isset($body['content']['blocks']) && is_array($body['content']['blocks'])) {
      $body['content']['blocks'] = $this->sanitizeBlocks($body['content']['blocks']);
    }
    return $body;
  }

  private function sanitizeBlocks(array $blocks): array {
    foreach ($blocks as $key => $block) {
      if (!is_array($block) || !isset($block['type'])) {
        continue;
      }
      if (isset($block['blocks']) && is_array($block['blocks'])) {
        $blocks[$key]['blocks'] = $this->sanitizeBlocks($block['blocks']);
      } else {
        $blocks[$key] = $this->sanitizeBlock($block);
      }
    };
    return $blocks;
  }

  private function sanitizeBlock(array $block): array {
    if (!isset(self::SANITIZATION_CONFIG[$block['type']])) {
      return $block;
    }
    foreach (self::SANITIZATION_CONFIG[$block['type']] as $property) {
      if (!isset($block[$property])) {
        continue;
      }
      $block[$property] = $this->htmlSanitizer->sanitize($block[$property]);
    }
    return $block;
  }
}
