<?php
namespace MailPoetVendor\Carbon\Doctrine;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
interface CarbonDoctrineType
{
 public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform);
 public function convertToPHPValue($value, AbstractPlatform $platform);
 public function convertToDatabaseValue($value, AbstractPlatform $platform);
}
