<?php
namespace MailPoetVendor\Doctrine\Common\Cache;
if (!defined('ABSPATH')) exit;
interface MultiGetCache
{
 public function fetchMultiple(array $keys);
}
