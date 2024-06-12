<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Processor;
if (!defined('ABSPATH')) exit;
class HostnameProcessor implements ProcessorInterface
{
 private static $host;
 public function __construct()
 {
 self::$host = (string) \gethostname();
 }
 public function __invoke(array $record) : array
 {
 $record['extra']['hostname'] = self::$host;
 return $record;
 }
}
