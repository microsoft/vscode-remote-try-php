<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function in_array;
class ColumnDiff
{
 public $oldColumnName;
 public $column;
 public $changedProperties = [];
 public $fromColumn;
 public function __construct($oldColumnName, Column $column, array $changedProperties = [], ?Column $fromColumn = null)
 {
 if ($fromColumn === null) {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4785', 'Not passing the $fromColumn to %s is deprecated.', __METHOD__);
 }
 $this->oldColumnName = $oldColumnName;
 $this->column = $column;
 $this->changedProperties = $changedProperties;
 $this->fromColumn = $fromColumn;
 }
 public function getOldColumn() : ?Column
 {
 return $this->fromColumn;
 }
 public function getNewColumn() : Column
 {
 return $this->column;
 }
 public function hasTypeChanged() : bool
 {
 return $this->hasChanged('type');
 }
 public function hasLengthChanged() : bool
 {
 return $this->hasChanged('length');
 }
 public function hasPrecisionChanged() : bool
 {
 return $this->hasChanged('precision');
 }
 public function hasScaleChanged() : bool
 {
 return $this->hasChanged('scale');
 }
 public function hasUnsignedChanged() : bool
 {
 return $this->hasChanged('unsigned');
 }
 public function hasFixedChanged() : bool
 {
 return $this->hasChanged('fixed');
 }
 public function hasNotNullChanged() : bool
 {
 return $this->hasChanged('notnull');
 }
 public function hasDefaultChanged() : bool
 {
 return $this->hasChanged('default');
 }
 public function hasAutoIncrementChanged() : bool
 {
 return $this->hasChanged('autoincrement');
 }
 public function hasCommentChanged() : bool
 {
 return $this->hasChanged('comment');
 }
 public function hasChanged($propertyName)
 {
 return in_array($propertyName, $this->changedProperties, \true);
 }
 public function getOldColumnName()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5622', '%s is deprecated. Use $fromColumn instead.', __METHOD__);
 if ($this->fromColumn !== null) {
 $name = $this->fromColumn->getName();
 $quote = $this->fromColumn->isQuoted();
 } else {
 $name = $this->oldColumnName;
 $quote = \false;
 }
 return new Identifier($name, $quote);
 }
}
