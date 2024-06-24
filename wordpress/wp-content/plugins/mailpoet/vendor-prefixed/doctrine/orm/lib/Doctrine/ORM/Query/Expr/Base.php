<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException;
use Stringable;
use function count;
use function get_class;
use function get_debug_type;
use function implode;
use function in_array;
use function is_string;
use function sprintf;
abstract class Base
{
 protected $preSeparator = '(';
 protected $separator = ', ';
 protected $postSeparator = ')';
 protected $allowedClasses = [];
 protected $parts = [];
 public function __construct($args = [])
 {
 $this->addMultiple($args);
 }
 public function addMultiple($args = [])
 {
 foreach ((array) $args as $arg) {
 $this->add($arg);
 }
 return $this;
 }
 public function add($arg)
 {
 if ($arg !== null && (!$arg instanceof self || $arg->count() > 0)) {
 // If we decide to keep Expr\Base instances, we can use this check
 if (!is_string($arg) && !in_array(get_class($arg), $this->allowedClasses, \true)) {
 throw new InvalidArgumentException(sprintf("Expression of type '%s' not allowed in this context.", get_debug_type($arg)));
 }
 $this->parts[] = $arg;
 }
 return $this;
 }
 public function count()
 {
 return count($this->parts);
 }
 public function __toString()
 {
 if ($this->count() === 1) {
 return (string) $this->parts[0];
 }
 return $this->preSeparator . implode($this->separator, $this->parts) . $this->postSeparator;
 }
}
