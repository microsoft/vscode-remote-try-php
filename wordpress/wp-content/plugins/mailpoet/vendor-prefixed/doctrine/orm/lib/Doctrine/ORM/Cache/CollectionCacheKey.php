<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use function implode;
use function ksort;
use function str_replace;
use function strtolower;
class CollectionCacheKey extends CacheKey
{
 public $ownerIdentifier;
 public $entityClass;
 public $association;
 public function __construct($entityClass, $association, array $ownerIdentifier)
 {
 ksort($ownerIdentifier);
 $this->ownerIdentifier = $ownerIdentifier;
 $this->entityClass = (string) $entityClass;
 $this->association = (string) $association;
 $this->hash = str_replace('\\', '.', strtolower($entityClass)) . '_' . implode(' ', $ownerIdentifier) . '__' . $association;
 }
}
