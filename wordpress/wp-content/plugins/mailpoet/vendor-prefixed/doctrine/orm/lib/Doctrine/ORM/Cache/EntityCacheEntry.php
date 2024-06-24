<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use function array_map;
class EntityCacheEntry implements CacheEntry
{
 public $data;
 public $class;
 public function __construct($class, array $data)
 {
 $this->class = $class;
 $this->data = $data;
 }
 public static function __set_state(array $values)
 {
 return new self($values['class'], $values['data']);
 }
 public function resolveAssociationEntries(EntityManagerInterface $em)
 {
 return array_map(static function ($value) use($em) {
 if (!$value instanceof AssociationCacheEntry) {
 return $value;
 }
 return $em->getReference($value->class, $value->identifier);
 }, $this->data);
 }
}
