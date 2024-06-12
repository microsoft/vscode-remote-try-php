<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\Node;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
abstract class FunctionNode extends Node
{
 public $name;
 public function __construct($name)
 {
 $this->name = $name;
 }
 public abstract function getSql(SqlWalker $sqlWalker);
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkFunction($this);
 }
 public abstract function parse(Parser $parser);
}
