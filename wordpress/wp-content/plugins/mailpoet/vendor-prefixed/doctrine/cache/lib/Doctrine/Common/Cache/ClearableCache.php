<?php
namespace MailPoetVendor\Doctrine\Common\Cache;
if (!defined('ABSPATH')) exit;
interface ClearableCache
{
 public function deleteAll();
}
