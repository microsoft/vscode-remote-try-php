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
 public function dump($obj)
 {
 static $ident = 0;
 $str = '';
 if ($obj instanceof Node) {
 $str .= get_debug_type($obj) . '(' . PHP_EOL;
 $props = get_object_vars($obj);
 foreach ($props as $name => $prop) {
 $ident += 4;
 $str .= str_repeat(' ', $ident) . '"' . $name . '": ' . $this->dump($prop) . ',' . PHP_EOL;
 $ident -= 4;
 }
 $str .= str_repeat(' ', $ident) . ')';
 } elseif (is_array($obj)) {
 $ident += 4;
 $str .= 'array(';
 $some = \false;
 foreach ($obj as $k => $v) {
 $str .= PHP_EOL . str_repeat(' ', $ident) . '"' . $k . '" => ' . $this->dump($v) . ',';
 $some = \true;
 }
 $ident -= 4;
 $str .= ($some ? PHP_EOL . str_repeat(' ', $ident) : '') . ')';
 } elseif (is_object($obj)) {
 $str .= 'instanceof(' . get_debug_type($obj) . ')';
 } else {
 $str .= var_export($obj, \true);
 }
 return $str;
 }
}
