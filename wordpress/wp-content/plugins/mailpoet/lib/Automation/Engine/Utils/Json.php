<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Utils;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;

class Json {
  public static function encode(array $value): string {
    $json = json_encode((object)$value, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    $error = json_last_error();
    if ($error || $json === false) {
      throw new InvalidStateException(json_last_error_msg(), (string)$error);
    }
    return $json;
  }

  public static function decode(string $json): array {
    $value = json_decode($json, true);
    $error = json_last_error();
    if ($error) {
      throw new InvalidStateException(json_last_error_msg(), (string)$error);
    }
    if (!is_array($value)) {
      throw Exceptions::jsonNotObject($json);
    }
    return $value;
  }
}
