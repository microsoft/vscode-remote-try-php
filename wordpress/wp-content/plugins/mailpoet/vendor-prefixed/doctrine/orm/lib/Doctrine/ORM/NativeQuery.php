<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use function array_values;
use function is_int;
use function key;
use function ksort;
final class NativeQuery extends AbstractQuery
{
 private $sql;
 public function setSQL($sql) : self
 {
 $this->sql = $sql;
 return $this;
 }
 public function getSQL() : string
 {
 return $this->sql;
 }
 protected function _doExecute()
 {
 $parameters = [];
 $types = [];
 foreach ($this->getParameters() as $parameter) {
 $name = $parameter->getName();
 $value = $this->processParameterValue($parameter->getValue());
 $type = $parameter->getValue() === $value ? $parameter->getType() : Query\ParameterTypeInferer::inferType($value);
 $parameters[$name] = $value;
 $types[$name] = $type;
 }
 if ($parameters && is_int(key($parameters))) {
 ksort($parameters);
 ksort($types);
 $parameters = array_values($parameters);
 $types = array_values($types);
 }
 return $this->_em->getConnection()->executeQuery($this->sql, $parameters, $types, $this->_queryCacheProfile);
 }
}
