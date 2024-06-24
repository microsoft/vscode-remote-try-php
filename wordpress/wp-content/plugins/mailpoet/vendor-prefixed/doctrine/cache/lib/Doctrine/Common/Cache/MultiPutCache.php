<?php
namespace MailPoetVendor\Doctrine\Common\Cache;
if (!defined('ABSPATH')) exit;
interface MultiPutCache
{
 public function saveMultiple(array $keysAndValues, $lifetime = 0);
}
