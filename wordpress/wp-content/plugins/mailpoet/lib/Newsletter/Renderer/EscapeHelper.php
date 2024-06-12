<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer;

if (!defined('ABSPATH')) exit;


class EscapeHelper {
  /**
   * @param string $string
   * @return string
   */
  public static function escapeHtmlText($string) {
    return htmlspecialchars((string)$string, ENT_NOQUOTES, 'UTF-8');
  }

  /**
   * @param string $string
   * @return string
   */
  public static function escapeHtmlAttr($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
  }

  /**
   * Escapes Style attributes, but preserves single quotes. Some email clients
   * (e.g. Yahoo webmail) don't support encoded quoted font names.
   * Previously used htmlspecialchars but switched to esc_attr which is more appropriate.
   * @param string $string
   * @return string
   */
  public static function escapeHtmlStyleAttr($string) {
    return str_replace('&#039;', "'", esc_attr((string)$string));
  }

  /**
   * @param string $string
   * @return string
   */
  public static function unescapeHtmlStyleAttr($string) {
    // This decodes entities which may have been added by esc_attr.
    return htmlspecialchars_decode((string)$string, ENT_QUOTES);
  }

  /**
   * @param string $string
   * @return string
   */
  public static function escapeHtmlLinkAttr($string) {
    $string = self::escapeHtmlAttr($string);
    if (preg_match('/\s*(javascript:|data:text|data:application)/ui', $string) === 1) {
      return '';
    }
    return $string;
  }
}
