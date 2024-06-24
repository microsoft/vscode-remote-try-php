<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
class CollectionCacheEntry implements CacheEntry
{
 public $identifiers;
 public function __construct(array $identifiers)
 {
 $this->identifiers = $identifiers;
 }
 public static function __set_state(array $values)
 {
 return new self($values['identifiers']);
 }
}
