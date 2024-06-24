<?php
namespace MailPoetVendor\Doctrine\Common\Cache;
if (!defined('ABSPATH')) exit;
interface FlushableCache
{
 public function flushAll();
}
