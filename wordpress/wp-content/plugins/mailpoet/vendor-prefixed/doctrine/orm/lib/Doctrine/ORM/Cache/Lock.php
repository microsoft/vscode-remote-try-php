<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use function time;
use function uniqid;
class Lock
{
 public $value;
 public $time;
 public function __construct(string $value, ?int $time = null)
 {
 $this->value = $value;
 $this->time = $time ?: time();
 }
 public static function createLockRead()
 {
 return new self(uniqid((string) time(), \true));
 }
}
