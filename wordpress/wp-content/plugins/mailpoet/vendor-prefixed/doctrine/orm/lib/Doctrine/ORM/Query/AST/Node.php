<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
use function get_debug_type;
use function get_object_vars;
use function is_array;
use function is_object;
use function str_repeat;
use function var_export;
use const PHP_EOL;
abstract class Node
{
 public function dispatch($walker)
 {
 throw ASTException::noDispatchForNode($this);
 }
 public function __toString()
 {
 return $this->dump($this);
 }
 public function dump($value)
 {
 static $ident = 0;
 $str = '';
 if ($value instanceof Node) {
 $str .= get_debug_type($value) . '(' . PHP_EOL;
 $props = get_object_vars($value);
 foreach ($props as $name => $prop) {
 $ident += 4;
 $str .= str_repeat(' ', $ident) . '"' . $name . '": ' . $this->dump($prop) . ',' . PHP_EOL;
 $ident -= 4;
 }
 $str .= str_repeat(' ', $ident) . ')';
 } elseif (is_array($value)) {
 $ident += 4;
 $str .= 'array(';
 $some = \false;
 foreach ($value as $k => $v) {
 $str .= PHP_EOL . str_repeat(' ', $ident) . '"' . $k . '" => ' . $this->dump($v) . ',';
 $some = \true;
 }
 $ident -= 4;
 $str .= ($some ? PHP_EOL . str_repeat(' ', $ident) : '') . ')';
 } elseif (is_object($value)) {
 $str .= 'instanceof(' . get_debug_type($value) . ')';
 } else {
 $str .= var_export($value, \true);
 }
 return $str;
 }
}
