<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Processor;
if (!defined('ABSPATH')) exit;
interface ProcessorInterface
{
 public function __invoke(array $record);
}
