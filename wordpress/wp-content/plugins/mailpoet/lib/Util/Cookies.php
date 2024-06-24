<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


use InvalidArgumentException;

class Cookies {
  const DEFAULT_OPTIONS = [
    'expires' => 0,
    'path' => '',
    'domain' => '',
    'secure' => false,
    'httponly' => false,
  ];

  public function set($name, $value, array $options = []) {
    if (headers_sent()) {
      return;
    }
    $options = $options + self::DEFAULT_OPTIONS;
    $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $error = json_last_error();
    if ($error || ($value === false)) {
      throw new InvalidArgumentException();
    }

    setcookie(
      $name,
      $value,
      $options
    );
  }

  public function get($name) {
    if (!array_key_exists($name, $_COOKIE)) {
      return null;
    }
    $value = json_decode(sanitize_text_field(wp_unslash(($_COOKIE[$name]))), true);
    $error = json_last_error();
    if ($error) {
      return null;
    }
    return $value;
  }

  public function delete($name) {
    unset($_COOKIE[$name]);
  }
}
