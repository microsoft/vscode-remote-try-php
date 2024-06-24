<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Util;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class FieldNameObfuscator {

  const OBFUSCATED_FIELD_PREFIX = 'form_field_';
  const HASH_LENGTH = 12;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function obfuscate($name) {
    $authKey = defined('AUTH_KEY') ? AUTH_KEY : '';
    $hash = substr(md5($authKey . $this->wp->homeUrl() . $name), 0, self::HASH_LENGTH);
    return self::OBFUSCATED_FIELD_PREFIX . base64_encode($hash . '_' . $name);
  }

  public function deobfuscate($name) {
    $decoded = base64_decode(substr($name, strlen(self::OBFUSCATED_FIELD_PREFIX)));
    return substr($decoded, self::HASH_LENGTH + 1);
  }

  public function deobfuscateFormPayload($data) {
    $result = [];
    foreach ($data as $key => $value) {
      $result[$this->deobfuscateField($key)] = $value;
    }
    return $result;
  }

  private function deobfuscateField($name) {
    if ($this->wasFieldObfuscated($name)) {
      return $this->deobfuscate($name);
    } else {
      return $name;
    }
  }

  private function wasFieldObfuscated($name) {
    return strpos($name, FieldNameObfuscator::OBFUSCATED_FIELD_PREFIX) === 0;
  }
}
