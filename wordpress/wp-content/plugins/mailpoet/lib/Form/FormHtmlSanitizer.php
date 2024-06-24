<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class FormHtmlSanitizer {

  /**
   * @var array
   * Configuration of allowed tags for form blocks that may contain some html.
   * Covers all tags available in the form editor's Rich Text component and which we allow in checkbox label.
   * This doesn't cover CustomHTML block.
   */
  const ALLOWED_HTML = [
    'a' => [
      'href' => true,
      'title' => true,
      'data-id' => true,
      'data-type' => true,
      'target' => true,
      'rel' => true,
    ],
    'br' => [],
    'code' => [],
    'em' => [],
    'img' => [
      'class' => true,
      'style' => true,
      'src' => true,
      'alt' => true,
    ],
    'kbd' => [],
    'span' => [
      'style' => true,
      'data-font' => true,
      'class' => true,
    ],
    'mark' => [
      'style' => true,
      'class' => true,
    ],
    'strong' => [],
    'sub' => [],
    'sup' => [],
    's' => [],
  ];
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function sanitize(string $html): string {
    return $this->wp->wpKses($html, self::ALLOWED_HTML);
  }
}
