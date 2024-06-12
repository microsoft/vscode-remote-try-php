<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Formatter;
if (!defined('ABSPATH')) exit;
interface FormatterInterface
{
 public function format(array $record);
 public function formatBatch(array $records);
}
