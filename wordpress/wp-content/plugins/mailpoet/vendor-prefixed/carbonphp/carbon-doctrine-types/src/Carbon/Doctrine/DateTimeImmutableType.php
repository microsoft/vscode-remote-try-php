<?php
namespace MailPoetVendor\Carbon\Doctrine;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\CarbonImmutable;
use MailPoetVendor\Doctrine\DBAL\Types\VarDateTimeImmutableType;
class DateTimeImmutableType extends VarDateTimeImmutableType implements CarbonDoctrineType
{
 use CarbonTypeConverter;
 protected function getCarbonClassName() : string
 {
 return CarbonImmutable::class;
 }
}
