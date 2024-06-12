<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
use function strtoupper;
class QuantifiedExpression extends Node
{
 public $type;
 public $subselect;
 public function __construct($subselect)
 {
 $this->subselect = $subselect;
 }
 public function isAll()
 {
 return strtoupper($this->type) === 'ALL';
 }
 public function isAny()
 {
 return strtoupper($this->type) === 'ANY';
 }
 public function isSome()
 {
 return strtoupper($this->type) === 'SOME';
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkQuantifiedExpression($this);
 }
}
