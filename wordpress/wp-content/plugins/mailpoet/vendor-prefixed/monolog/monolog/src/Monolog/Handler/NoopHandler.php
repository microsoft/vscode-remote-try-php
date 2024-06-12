<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
class NoopHandler extends Handler
{
 public function isHandling(array $record) : bool
 {
 return \true;
 }
 public function handle(array $record) : bool
 {
 return \false;
 }
}
