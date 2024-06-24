<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\DB2Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\OraclePlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use function method_exists;
use function strtolower;
use function strtoupper;
trait SQLResultCasing
{
 private function getSQLResultCasing(AbstractPlatform $platform, string $column) : string
 {
 if ($platform instanceof DB2Platform || $platform instanceof OraclePlatform) {
 return strtoupper($column);
 }
 if ($platform instanceof PostgreSQLPlatform) {
 return strtolower($column);
 }
 if (method_exists(AbstractPlatform::class, 'getSQLResultCasing')) {
 return $platform->getSQLResultCasing($column);
 }
 return $column;
 }
}
