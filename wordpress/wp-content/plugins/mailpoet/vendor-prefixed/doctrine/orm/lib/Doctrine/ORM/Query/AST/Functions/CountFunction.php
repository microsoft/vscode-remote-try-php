<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\DBAL\Types\Types;
use MailPoetVendor\Doctrine\ORM\Query\AST\AggregateExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\TypedExpression;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
final class CountFunction extends FunctionNode implements TypedExpression
{
 private $aggregateExpression;
 public function getSql(SqlWalker $sqlWalker) : string
 {
 return $this->aggregateExpression->dispatch($sqlWalker);
 }
 public function parse(Parser $parser) : void
 {
 $this->aggregateExpression = $parser->AggregateExpression();
 }
 public function getReturnType() : Type
 {
 return Type::getType(Types::INTEGER);
 }
}
