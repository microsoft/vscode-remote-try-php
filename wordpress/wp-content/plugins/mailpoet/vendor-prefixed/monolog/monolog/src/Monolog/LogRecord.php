<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog;
if (!defined('ABSPATH')) exit;
use ArrayAccess;
interface LogRecord extends \ArrayAccess
{
 public function toArray() : array;
}
