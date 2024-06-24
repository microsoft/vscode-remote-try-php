<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\Types;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Types\Type;

class SerializedArrayType extends Type {
  const NAME = 'serialized_array';

  public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
    return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
  }

  public function convertToDatabaseValue($value, AbstractPlatform $platform) {
    return \serialize($value);
  }

  public function convertToPHPValue($value, AbstractPlatform $platform) {
    if ($value === null) {
      return null;
    }
    $value = \is_resource($value) ? \stream_get_contents($value) : $value;
    $val = \unserialize($value);
    if ($val === \false && $value !== 'b:0;') {
      return null;
    }
    return $val;
  }

  public function getName() {
    return self::NAME;
  }

  public function requiresSQLCommentHint(AbstractPlatform $platform) {
    return true;
  }
}
