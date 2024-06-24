<?php
namespace MailPoetVendor\Carbon\Doctrine;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Types\VarDateTimeType;
class DateTimeType extends VarDateTimeType implements CarbonDoctrineType
{
 use CarbonTypeConverter;
}
