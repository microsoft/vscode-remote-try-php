<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Processor;
if (!defined('ABSPATH')) exit;
class MemoryUsageProcessor extends MemoryProcessor
{
 public function __invoke(array $record) : array
 {
 $usage = \memory_get_usage($this->realUsage);
 if ($this->useFormatting) {
 $usage = $this->formatBytes($usage);
 }
 $record['extra']['memory_usage'] = $usage;
 return $record;
 }
}
