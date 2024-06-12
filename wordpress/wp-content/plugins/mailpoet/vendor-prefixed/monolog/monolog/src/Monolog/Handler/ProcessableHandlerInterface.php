<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Monolog\Processor\ProcessorInterface;
interface ProcessableHandlerInterface
{
 public function pushProcessor(callable $callback) : HandlerInterface;
 public function popProcessor() : callable;
}
