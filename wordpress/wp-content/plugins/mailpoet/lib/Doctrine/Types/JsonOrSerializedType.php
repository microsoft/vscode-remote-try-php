<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\Types;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;

class JsonOrSerializedType extends JsonType {
  const NAME = 'json_or_serialized';

  public function convertToPHPValue($value, AbstractPlatform $platform) {
    if ($value === null) {
      return null;
    }

    if (is_resource($value)) {
      $value = stream_get_contents($value);
    }

    if (is_serialized($value)) {
      return unserialize($value);
    }
    return parent::convertToPHPValue($value, $platform);
  }

  public function getName() {
    return self::NAME;
  }
}
