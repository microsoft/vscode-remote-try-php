<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use function implode;
use function ksort;
use function str_replace;
use function strtolower;
class EntityCacheKey extends CacheKey
{
 public $identifier;
 public $entityClass;
 public function __construct($entityClass, array $identifier)
 {
 ksort($identifier);
 $this->identifier = $identifier;
 $this->entityClass = $entityClass;
 parent::__construct(str_replace('\\', '.', strtolower($entityClass) . '_' . implode(' ', $identifier)));
 }
}
