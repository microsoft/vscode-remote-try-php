<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
interface HandlerInterface
{
 public function isHandling(array $record) : bool;
 public function handle(array $record) : bool;
 public function handleBatch(array $records) : void;
 public function close() : void;
}
