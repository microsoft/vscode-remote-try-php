<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class NewsletterHtmlSanitizer {
  /** @var WPFunctions */
  private $wp;

  /**
   * @var array
   * Configuration of allowed tags for form blocks that may contain some html.
   * Covers all tags available in the form editor's Rich Text component
   */
  private $allowedHtml = [
    'p' => [
      'class' => true,
      'style' => true,
    ],
    'span' => [
      'class' => true,
      'style' => true,
    ],
    'a' => [
      'href' => true,
      'class' => true,
      'title' => true,
      'target' => true,
      'style' => true,
    ],
    'h1' => [
      'class' => true,
      'style' => true,
    ],
    'h2' => [
      'class' => true,
      'style' => true,
    ],
    'h3' => [
      'class' => true,
      'style' => true,
    ],
    'ol' => [
      'class' => true,
      'style' => true,
    ],
    'ul' => [
      'class' => true,
      'style' => true,
    ],
    'li' => [
      'class' => true,
      'style' => true,
    ],
    'strong' => [
      'class' => true,
      'style' => true,
    ],
    'em' => [
      'class' => true,
      'style' => true,
    ],
    'strike' => [],
    'br' => [],
    'blockquote' => [
      'class' => true,
      'style' => true,
    ],
    'table' => [
      'class' => true,
      'style' => true,
    ],
    'tr' => [
      'class' => true,
      'style' => true,
    ],
    'th' => [
      'class' => true,
      'style' => true,
    ],
    'td' => [
      'class' => true,
      'style' => true,
    ],
    'del' => [],
  ];

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function sanitize(string $html): string {
    // Because wpKses break shortcodes we prefix shortcodes with http protocol
    $html = str_replace('href="[', 'href="http://[', $html);
    $this->wp->addFilter('safecss_filter_attr_allow_css', [$this, 'allowRgbInCss'], 10, 2);
    $html = $this->wp->wpKses($html, $this->allowedHtml);
    $this->wp->removeFilter('safecss_filter_attr_allow_css', [$this, 'allowRgbInCss'], 10);
    $html = str_replace('href="http://[', 'href="[', $html);
    return $html;
  }

  /**
   * At the moment rgb() is not allowed to use in the style attribute. `style="color:rgb(0,0,0);"` gets
   * sanitized if you use wp_kses. We hook into safecss_filter_attr_allow_css to allow for rgb. The code
   * follows the precedent WordPress sets for the usage of var(), calc() etc. in safecss_filter_attr()
   */
  public function allowRgbInCss($allowed, $cssString): bool {
    if ($allowed) {
      return (bool)$allowed;
    }
    $cssString = preg_replace(
      '/\b(?:rgb)(\((?:[^()]|(?1))*\))/',
      '',
      $cssString
    );
    return !preg_match('%[\\\(&=}]|/\*%', $cssString);
  }

  public function sanitizeURL(string $url): string {
    return $this->wp->escUrlRaw($url);
  }
}
