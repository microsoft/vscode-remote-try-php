<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\Types;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Types\Type;

class JsonType extends Type {
  const NAME = 'json';

  public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
    return $platform->getJsonTypeDeclarationSQL($fieldDeclaration);
  }

  public function convertToDatabaseValue($value, AbstractPlatform $platform) {
    if ($value === null) {
      return null;
    }

    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    if (defined('JSON_PRESERVE_ZERO_FRACTION')) {
      $flags |= JSON_PRESERVE_ZERO_FRACTION; // phpcs:ignore
    }

    $encoded = json_encode($value, $flags);
    $this->handleErrors();
    return $encoded;
  }

  public function convertToPHPValue($value, AbstractPlatform $platform) {
    if ($value === null || $value === '') {
      return null;
    }

    if (is_resource($value)) {
      $value = stream_get_contents($value);
    }

    $value = mb_convert_encoding((string)$value, 'UTF-8', 'UTF-8'); // sanitize invalid utf8
    $decoded = json_decode($value, true);
    $this->handleErrors();
    return $decoded;
  }

  public function getName() {
    return self::NAME;
  }

  public function requiresSQLCommentHint(AbstractPlatform $platform) {
    return !$platform->hasNativeJsonType();
  }

  private function handleErrors() {
    $error = json_last_error();
    if ($error !== JSON_ERROR_NONE) {
      throw new \RuntimeException('Error when parsing JSON database value: "' . json_last_error_msg() . '"', $error);
    }
  }
}
