<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Persister;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\Region;
interface CachedPersister
{
 public function afterTransactionComplete();
 public function afterTransactionRolledBack();
 public function getCacheRegion();
}
