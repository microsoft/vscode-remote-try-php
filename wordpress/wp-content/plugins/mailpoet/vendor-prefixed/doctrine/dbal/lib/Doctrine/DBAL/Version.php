<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use function str_replace;
use function strtoupper;
use function version_compare;
class Version
{
 public const VERSION = '2.13.8';
 public static function compare($version)
 {
 $version = str_replace(' ', '', strtoupper($version));
 return version_compare($version, self::VERSION);
 }
}
