<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function preg_replace;
use function str_contains;
use function strrpos;
use function strtolower;
use function strtoupper;
use function substr;
use const CASE_LOWER;
use const CASE_UPPER;
class UnderscoreNamingStrategy implements NamingStrategy
{
 private const DEFAULT_PATTERN = '/(?<=[a-z])([A-Z])/';
 private const NUMBER_AWARE_PATTERN = '/(?<=[a-z0-9])([A-Z])/';
 private $case;
 private $pattern;
 public function __construct($case = CASE_LOWER, bool $numberAware = \false)
 {
 if (!$numberAware) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/7908', 'Creating %s without setting second argument $numberAware=true is deprecated and will be removed in Doctrine ORM 3.0.', self::class);
 }
 $this->case = $case;
 $this->pattern = $numberAware ? self::NUMBER_AWARE_PATTERN : self::DEFAULT_PATTERN;
 }
 public function getCase()
 {
 return $this->case;
 }
 public function setCase($case)
 {
 $this->case = $case;
 }
 public function classToTableName($className)
 {
 if (str_contains($className, '\\')) {
 $className = substr($className, strrpos($className, '\\') + 1);
 }
 return $this->underscore($className);
 }
 public function propertyToColumnName($propertyName, $className = null)
 {
 return $this->underscore($propertyName);
 }
 public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null)
 {
 return $this->underscore($propertyName) . '_' . $embeddedColumnName;
 }
 public function referenceColumnName()
 {
 return $this->case === CASE_UPPER ? 'ID' : 'id';
 }
 public function joinColumnName($propertyName, $className = null)
 {
 return $this->underscore($propertyName) . '_' . $this->referenceColumnName();
 }
 public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
 {
 return $this->classToTableName($sourceEntity) . '_' . $this->classToTableName($targetEntity);
 }
 public function joinKeyColumnName($entityName, $referencedColumnName = null)
 {
 return $this->classToTableName($entityName) . '_' . ($referencedColumnName ?: $this->referenceColumnName());
 }
 private function underscore(string $string) : string
 {
 $string = preg_replace($this->pattern, '_$1', $string);
 if ($this->case === CASE_UPPER) {
 return strtoupper($string);
 }
 return strtolower($string);
 }
}
