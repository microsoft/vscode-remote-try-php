<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
use function implode;
class Func
{
 protected $name;
 protected $arguments;
 public function __construct($name, $arguments)
 {
 $this->name = $name;
 $this->arguments = (array) $arguments;
 }
 public function getName()
 {
 return $this->name;
 }
 public function getArguments()
 {
 return $this->arguments;
 }
 public function __toString()
 {
 return $this->name . '(' . implode(', ', $this->arguments) . ')';
 }
}
