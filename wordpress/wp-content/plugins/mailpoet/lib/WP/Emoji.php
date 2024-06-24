<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\WP;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Emoji {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp = null
  ) {
    if ($wp === null) {
      $wp = new WPFunctions();
    }
    $this->wp = $wp;
  }

  public function encodeEmojisInBody($newsletterRenderedBody) {
    if (is_array($newsletterRenderedBody)) {
      return array_map([$this, 'encodeRenderedBodyForUTF8Column'], $newsletterRenderedBody);
    }
    return $this->encodeRenderedBodyForUTF8Column($newsletterRenderedBody);
  }

  public function decodeEmojisInBody($newsletterRenderedBody) {
    if (is_array($newsletterRenderedBody)) {
      return array_map([$this, 'decodeEntities'], $newsletterRenderedBody);
    }
    return $this->decodeEntities($newsletterRenderedBody);
  }

  public function sanitizeEmojisInFormBody(array $body): array {
    $bodyJson = json_encode($body, JSON_UNESCAPED_UNICODE);
    $fixedJson = $this->encodeForUTF8Column(MP_FORMS_TABLE, 'body', $bodyJson);
    return json_decode($fixedJson, true);
  }

  private function encodeRenderedBodyForUTF8Column($value) {
    return $this->encodeForUTF8Column(
      MP_SENDING_QUEUES_TABLE,
      'newsletter_rendered_body',
      $value
    );
  }

  public function encodeForUTF8Column($table, $field, $value) {
    global $wpdb;
    $charset = $wpdb->get_col_charset($table, $field);
    // utf8 doesn't support emojis, so we need to encode them
    // utf8 was an alias for utf8mb3, but it was dropped in MySQL 8.0.28 so we need to check both
    if ($charset === 'utf8' || $charset === 'utf8mb3') {
      $value = $this->wp->wpEncodeEmoji($value);
    }
    return $value;
  }

  public function decodeEntities($content) {
    // Based on WPFunctions::get()->wpStaticizeEmoji()

    // Loosely match the Emoji Unicode range.
    $regex = '/(&#x[2-3][0-9a-f]{3};|&#x1f[1-6][0-9a-f]{2};)/';

    $matches = [];
    if (preg_match_all($regex, $content, $matches)) {
      if (!empty($matches[1])) {
        foreach ($matches[1] as $emoji) {
          $entity = html_entity_decode($emoji, ENT_COMPAT, 'UTF-8');
          $content = str_replace($emoji, $entity, $content);
        }
      }
    }

    return $content;
  }
}
