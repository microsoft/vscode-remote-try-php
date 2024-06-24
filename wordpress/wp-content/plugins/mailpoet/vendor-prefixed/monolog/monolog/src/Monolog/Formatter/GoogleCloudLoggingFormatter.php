<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Formatter;
if (!defined('ABSPATH')) exit;
use DateTimeInterface;
use MailPoetVendor\Monolog\LogRecord;
final class GoogleCloudLoggingFormatter extends JsonFormatter
{
 public function format(array $record) : string
 {
 // Re-key level for GCP logging
 $record['severity'] = $record['level_name'];
 $record['time'] = $record['datetime']->format(DateTimeInterface::RFC3339_EXTENDED);
 // Remove keys that are not used by GCP
 unset($record['level'], $record['level_name'], $record['datetime']);
 return parent::format($record);
 }
}
