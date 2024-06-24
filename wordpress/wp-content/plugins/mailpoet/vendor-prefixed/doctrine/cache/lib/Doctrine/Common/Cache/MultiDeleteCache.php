<?php
namespace MailPoetVendor\Doctrine\Common\Cache;
if (!defined('ABSPATH')) exit;
interface MultiDeleteCache
{
 public function deleteMultiple(array $keys);
}
