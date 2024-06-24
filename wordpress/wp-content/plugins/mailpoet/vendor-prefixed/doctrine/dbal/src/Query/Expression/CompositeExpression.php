<?php
namespace MailPoetVendor\Doctrine\DBAL\Query\Expression;
if (!defined('ABSPATH')) exit;
use Countable;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use ReturnTypeWillChange;
use function array_merge;
use function count;
use function implode;
class CompositeExpression implements Countable
{
 public const TYPE_AND = 'AND';
 public const TYPE_OR = 'OR';
 private $type;
 private array $parts = [];
 public function __construct($type, array $parts = [])
 {
 $this->type = $type;
 $this->addMultiple($parts);
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3864', 'Do not use CompositeExpression constructor directly, use static and() and or() factory methods.');
 }
 public static function and($part, ...$parts) : self
 {
 return new self(self::TYPE_AND, array_merge([$part], $parts));
 }
 public static function or($part, ...$parts) : self
 {
 return new self(self::TYPE_OR, array_merge([$part], $parts));
 }
 public function addMultiple(array $parts = [])
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3844', 'CompositeExpression::addMultiple() is deprecated, use CompositeExpression::with() instead.');
 foreach ($parts as $part) {
 $this->add($part);
 }
 return $this;
 }
 public function add($part)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3844', 'CompositeExpression::add() is deprecated, use CompositeExpression::with() instead.');
 if ($part === null) {
 return $this;
 }
 if ($part instanceof self && count($part) === 0) {
 return $this;
 }
 $this->parts[] = $part;
 return $this;
 }
 public function with($part, ...$parts) : self
 {
 $that = clone $this;
 $that->parts = array_merge($that->parts, [$part], $parts);
 return $that;
 }
 #[\ReturnTypeWillChange]
 public function count()
 {
 return count($this->parts);
 }
 public function __toString()
 {
 if ($this->count() === 1) {
 return (string) $this->parts[0];
 }
 return '(' . implode(') ' . $this->type . ' (', $this->parts) . ')';
 }
 public function getType()
 {
 return $this->type;
 }
}
