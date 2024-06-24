<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function func_num_args;
class InstanceOfExpression extends Node
{
 public $not;
 public $identificationVariable;
 public $value;
 public function __construct($identVariable, array $value = [], bool $not = \false)
 {
 if (func_num_args() < 2) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/10267', 'Not passing a value for $value to %s() is deprecated.', __METHOD__);
 }
 $this->identificationVariable = $identVariable;
 $this->value = $value;
 $this->not = $not;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkInstanceOfExpression($this);
 }
}
