<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class Literal extends Node
{
 public const STRING = 1;
 public const BOOLEAN = 2;
 public const NUMERIC = 3;
 public $type;
 public $value;
 public function __construct($type, $value)
 {
 $this->type = $type;
 $this->value = $value;
 }
 public function dispatch($walker)
 {
 return $walker->walkLiteral($this);
 }
}
