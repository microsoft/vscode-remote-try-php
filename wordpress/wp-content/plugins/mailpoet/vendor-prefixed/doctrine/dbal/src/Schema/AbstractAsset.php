<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function array_map;
use function crc32;
use function dechex;
use function explode;
use function implode;
use function str_replace;
use function strpos;
use function strtolower;
use function strtoupper;
use function substr;
abstract class AbstractAsset
{
 protected $_name = '';
 protected $_namespace;
 protected $_quoted = \false;
 protected function _setName($name)
 {
 if ($this->isIdentifierQuoted($name)) {
 $this->_quoted = \true;
 $name = $this->trimQuotes($name);
 }
 if (strpos($name, '.') !== \false) {
 $parts = explode('.', $name);
 $this->_namespace = $parts[0];
 $name = $parts[1];
 }
 $this->_name = $name;
 }
 public function isInDefaultNamespace($defaultNamespaceName)
 {
 return $this->_namespace === $defaultNamespaceName || $this->_namespace === null;
 }
 public function getNamespaceName()
 {
 return $this->_namespace;
 }
 public function getShortestName($defaultNamespaceName)
 {
 $shortestName = $this->getName();
 if ($this->_namespace === $defaultNamespaceName) {
 $shortestName = $this->_name;
 }
 return strtolower($shortestName);
 }
 public function getFullQualifiedName($defaultNamespaceName)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4814', 'AbstractAsset::getFullQualifiedName() is deprecated.' . ' Use AbstractAsset::getNamespaceName() and ::getName() instead.');
 $name = $this->getName();
 if ($this->_namespace === null) {
 $name = $defaultNamespaceName . '.' . $name;
 }
 return strtolower($name);
 }
 public function isQuoted()
 {
 return $this->_quoted;
 }
 protected function isIdentifierQuoted($identifier)
 {
 return isset($identifier[0]) && ($identifier[0] === '`' || $identifier[0] === '"' || $identifier[0] === '[');
 }
 protected function trimQuotes($identifier)
 {
 return str_replace(['`', '"', '[', ']'], '', $identifier);
 }
 public function getName()
 {
 if ($this->_namespace !== null) {
 return $this->_namespace . '.' . $this->_name;
 }
 return $this->_name;
 }
 public function getQuotedName(AbstractPlatform $platform)
 {
 $keywords = $platform->getReservedKeywordsList();
 $parts = explode('.', $this->getName());
 foreach ($parts as $k => $v) {
 $parts[$k] = $this->_quoted || $keywords->isKeyword($v) ? $platform->quoteIdentifier($v) : $v;
 }
 return implode('.', $parts);
 }
 protected function _generateIdentifierName($columnNames, $prefix = '', $maxSize = 30)
 {
 $hash = implode('', array_map(static function ($column) : string {
 return dechex(crc32($column));
 }, $columnNames));
 return strtoupper(substr($prefix . '_' . $hash, 0, $maxSize));
 }
}
