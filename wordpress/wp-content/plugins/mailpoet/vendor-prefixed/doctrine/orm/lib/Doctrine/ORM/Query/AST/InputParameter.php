<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\QueryException;
use function is_numeric;
use function strlen;
use function substr;
class InputParameter extends Node
{
 public $isNamed;
 public $name;
 public function __construct($value)
 {
 if (strlen($value) === 1) {
 throw QueryException::invalidParameterFormat($value);
 }
 $param = substr($value, 1);
 $this->isNamed = !is_numeric($param);
 $this->name = $param;
 }
 public function dispatch($walker)
 {
 return $walker->walkInputParameter($this);
 }
}
