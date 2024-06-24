<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
trait WebRequestRecognizerTrait
{
 protected function isWebRequest() : bool
 {
 return 'cli' !== \PHP_SAPI && 'phpdbg' !== \PHP_SAPI;
 }
}
