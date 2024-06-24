<?php
namespace MailPoetVendor\Symfony\Component\Finder\Comparator;
if (!defined('ABSPATH')) exit;
class Comparator
{
 private $target;
 private $operator = '==';
 public function getTarget()
 {
 return $this->target;
 }
 public function setTarget($target)
 {
 $this->target = $target;
 }
 public function getOperator()
 {
 return $this->operator;
 }
 public function setOperator($operator)
 {
 if (!$operator) {
 $operator = '==';
 }
 if (!\in_array($operator, ['>', '<', '>=', '<=', '==', '!='])) {
 throw new \InvalidArgumentException(\sprintf('Invalid operator "%s".', $operator));
 }
 $this->operator = $operator;
 }
 public function test($test)
 {
 switch ($this->operator) {
 case '>':
 return $test > $this->target;
 case '>=':
 return $test >= $this->target;
 case '<':
 return $test < $this->target;
 case '<=':
 return $test <= $this->target;
 case '!=':
 return $test != $this->target;
 }
 return $test == $this->target;
 }
}
