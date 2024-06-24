<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
class AssociationCacheEntry implements CacheEntry
{
 public $identifier;
 public $class;
 public function __construct($class, array $identifier)
 {
 $this->class = $class;
 $this->identifier = $identifier;
 }
 public static function __set_state(array $values)
 {
 return new self($values['class'], $values['identifier']);
 }
}
