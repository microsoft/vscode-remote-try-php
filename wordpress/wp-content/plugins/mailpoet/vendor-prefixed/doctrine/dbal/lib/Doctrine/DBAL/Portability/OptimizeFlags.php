<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Portability;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\DB2Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\OraclePlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\SQLAnywhere16Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\SqlitePlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\SQLServer2012Platform;
final class OptimizeFlags
{
 private static $platforms = [DB2Platform::class => 0, OraclePlatform::class => Connection::PORTABILITY_EMPTY_TO_NULL, PostgreSQL94Platform::class => 0, SQLAnywhere16Platform::class => 0, SqlitePlatform::class => 0, SQLServer2012Platform::class => 0];
 public function __invoke(AbstractPlatform $platform, int $flags) : int
 {
 foreach (self::$platforms as $class => $mask) {
 if ($platform instanceof $class) {
 $flags &= ~$mask;
 break;
 }
 }
 return $flags;
 }
}
