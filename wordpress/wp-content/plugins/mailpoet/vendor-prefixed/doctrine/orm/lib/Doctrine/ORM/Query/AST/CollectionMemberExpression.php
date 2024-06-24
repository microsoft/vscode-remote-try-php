<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class CollectionMemberExpression extends Node
{
 public $entityExpression;
 public $collectionValuedPathExpression;
 public $not;
 public function __construct($entityExpr, $collValuedPathExpr, bool $not = \false)
 {
 $this->entityExpression = $entityExpr;
 $this->collectionValuedPathExpression = $collValuedPathExpr;
 $this->not = $not;
 }
 public function dispatch($walker)
 {
 return $walker->walkCollectionMemberExpression($this);
 }
}
